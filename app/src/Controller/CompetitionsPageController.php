<?php

namespace App\Controller;

use App\Model\Competition;
use App\Model\Registration;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Security\Security;
use SilverStripe\Omnipay\GatewayInfo;
use SilverStripe\Omnipay\Model\Payment;
use SilverStripe\Omnipay\Service\ServiceFactory;
use SilverStripe\Omnipay\GatewayFieldsFactory;

class CompetitionsPageController extends PageController
{
    private static $allowed_actions = [
        'competition',
        'register',
        'RegistrationForm',
        'PaymentForm',
    ];

    private static $url_handlers = [
        'RegistrationForm' => 'RegistrationForm',
        'PaymentForm' => 'PaymentForm',
        '$WCAID/register' => 'register',
        '$WCAID!' => 'competition',
    ];

    public function competition()
    {
        $competition = $this->getCompetition();
        return $this->customise(['Competition' => $competition])->renderWith(['Competition', 'Page']);
    }

    public function register()
    {
        $competition = $this->getCompetition();
        return $this->customise(['Competition' => $competition])->renderWith(['Competition_register', 'Page']);
    }

    private function getCompetition()
    {
        $competition = null;
        if ( ($WCAID = $this->getRequest()->param('WCAID')) || ($WCAID = $this->getRequest()->postVar('WCAID')) ) {
            $competition = Competition::get()->filter(['WCAID' => $WCAID])->first();
        }
        return $competition;
    }

    public function RegistrationForm()
    {
        if ($member = Security::getCurrentUser()) {
            $competition = $this->getCompetition();
            $events = $competition->CompetitionEvents();

            $form = Form::create(
                $this,
                __FUNCTION__
            );

            if ($registration = $this->getRegistration()) {
                $form->setFields(
                    FieldList::create(
                        HiddenField::create('WCAID', 'WCAID', $competition->WCAID),
                        ReadonlyField::create('RegistrationFee', 'Registration Fee', $registration->FeePayable()->Nice()),
                        CheckboxSetField::create('CompetitionEvents', 'Events', $events->map('ID', 'Name'), $registration->CompetitionEvents()),
                        TextareaField::create('Comments', 'Comments', $registration->Comments ? $registration->Comments : ' '),
                        CheckboxField::create('AcceptsMarketing', 'Subscribe to receive emails about upcoming competitions in New Zealand.')
                    )
                );

                $form->makeReadonly();
            } else {
                if ($registrationPeriods = $competition->RegistrationPeriods()) {
                    $registrationFees = '';
                    foreach ($registrationPeriods as $period) {
                        $registrationFees .= $period->BaseFee->Nice() . ' until ' . $period->dbObject('EndDate')->Format('MMM d') . '. ';
                    }
                } else {
                    $registrationFees = $competition->BaseFee->Nice();
                }

                $form->setFields(
                    FieldList::create(
                        HiddenField::create('WCAID', 'WCAID', $competition->WCAID),
                        ReadonlyField::create('RegistrationFee', ($competition->HasEventFees() ? 'Base Registration Fee' : 'Registration Fee'), $registrationFees),
                        CheckboxSetField::create('CompetitionEvents', 'Events', $events->map('ID', ($competition->HasEventFees() ? 'NameWithFee' : 'Name'))),
                        TextareaField::create('Comments', 'Comments', ''),
                        CheckboxField::create('AcceptsMarketing', 'Subscribe to receive emails about upcoming competitions in New Zealand.')
                    )
                );

                $form->setActions(
                    FieldList::create(
                        FormAction::create('handleRegister','Register')
                            ->setUseButtonTag(true)
                    )
                );

                $form->setValidator(
                    RequiredFields::create('CompetitionEvents')
                );
            }


            return $form;
        }
    }

    public function handleRegister($data, Form $form)
    {
        if ($member = Security::getCurrentUser()) {
            $competition = $this->getCompetition();
            if (!$registration = $this->getRegistration()) {
                $registration = Registration::create();
                $registration->MemberID = $member->ID;
                $registration->CompetitionID = $competition->ID;
            }
            $form->saveInto($registration);
            $registration->write();

            if ($data['AcceptsMarketing']) {
                $member->AcceptsMarketing = true;
                $member->write();
            }

            $form->sessionMessage('Thank you, your registration has been received.','good');
        }
        return $this->redirectBack();
    }

    protected function getRegistration()
    {
        $registration = null;
        $competition = $this->getCompetition();
        $member = Security::getCurrentUser();
        if ($competition && $member) {
            $registration = Registration::get()->filter(['MemberID' => $member->ID, 'CompetitionID' => $competition->ID])->first();
        }
        return $registration;
    }

    public function PaymentForm()
    {
        $form = Form::create($this, __FUNCTION__);

        if ($registration = $this->getRegistration()) {
            $competition = $this->getCompetition();
            $gateways = GatewayInfo::getSupportedGateways();
    
            $payments = $this->getRegistration()->Payments();
    
            if ($paid = $payments->filter(['Status' => 'Captured'])->first()) {
                $form->sessionMessage('Payment received, thank you!','good');
            } else {
                $form->setFields(
                    FieldList::create(
                        HiddenField::create('WCAID', 'WCAID', $competition->WCAID),
                        OptionsetField::create(
                            'PaymentMethod',
                            'Please choose how you would like to pay',
                            GatewayInfo::getSupportedGateways()
                        )
                    )
                );
    
                $form->setActions(
                    FieldList::create(FormAction::create(
                        'handlePayment',
                        'Pay'
                    ))
                );
    
                $form->setValidator(
                    RequiredFields::create('PaymentMethod')
                );
    
                if ($deposit = $payments->filter(['Gateway' => 'Manual', 'Status' => 'Authorized'])->first()) {
                    $form->Fields()->push(
                        ReadonlyField::create('BankDetails', 'Bank Deposit Details', 'Kiwibank' . PHP_EOL . 'Speedcubing New Zealand' . PHP_EOL . '38-9011-0738757-00')
                    );
                }
            }
        }

        return $form;
    }

    public function handlePayment($data, Form $form)
    {
        if ($member = Security::getCurrentUser()) {
            $competition = $this->getCompetition();
            if ($registration = $this->getRegistration()) {
                $paymentDue = $registration->FeePayable();

                $payment = Payment::create()
                    ->init($data['PaymentMethod'], $paymentDue->Amount, $paymentDue->Currency)
                    ->setSuccessUrl($competition->Link('register'));

                $payment->RegistrationID = $registration->ID;

                $payment->write();

                $response = ServiceFactory::create()
                    ->getService($payment, ServiceFactory::INTENT_PAYMENT)
                    ->initiate([
                        'description' => 'Registration for ' . $competition->Name
                    ]);
                    
                return $response->redirectOrRespond();
            }
        }
        return $this->redirectBack();
    }
}
