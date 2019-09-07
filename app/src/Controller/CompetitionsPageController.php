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
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Security\Security;

class CompetitionsPageController extends PageController
{
    private static $allowed_actions = [
        'competition',
        'register',
        'RegistrationForm',
    ];

    private static $url_handlers = [
        'RegistrationForm' => 'RegistrationForm',
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
                $form->sessionMessage('Thank you, your registration has been received.','good');
            } else {
                $form->setFields(
                    FieldList::create(
                        HiddenField::create('WCAID', 'WCAID', $competition->WCAID),
                        ReadonlyField::create('RegistrationFee', 'Registration Fee', '$45'),
                        CheckboxSetField::create('CompetitionEvents', 'Events', $events->map('ID', 'Name')),
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
}
