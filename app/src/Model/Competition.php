<?php

namespace App\Model;

use App\Model\CompetitionsPage;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldExportButton;

class Competition extends DataObject
{
    private static $db = [
        'WCAID' => 'Varchar',
        'WCAURL' => 'Varchar',
        'Name' => 'Varchar',
        'City' => 'Varchar',
        'Venue' => 'Varchar',
        'VenueAddress' => 'Varchar',
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'RegistrationOpen' => 'Datetime',
        'RegistrationClose' => 'Datetime',
        'BaseFee' => 'Money',
    ];

    private static $has_many = [
        'CompetitionEvents' => CompetitionEvent::class,
        'Registrations' => Registration::class,
    ];

    private static $many_many = [
        'Events' => [
            'through' => CompetitionEvent::class,
            'from' => 'Competition',
            'to' => 'Event',
        ]
    ];

    public function populateDefaults() 
    {
        $this->BaseFee = DBMoney::create()->setCurrency('NZD');
        parent::populateDefaults();
    }

    public function getCMSfields()
    {
        $fields = parent::getCMSFields();

        if ($gridField = $fields->dataFieldByName('Registrations')) {
            $wcaExportColumns = [
                'WCAStatus' => 'Status',
                'Member.Name' => 'Name',
                'Member.WCACountryID' => 'Country',
                'Member.WCAID' => 'WCA ID',
                'Member.Birthdate' => 'Birth Date',
                'Member.Gender' => 'Gender',
                'Member.Email' => 'Email',
            ];

            foreach ($this->CompetitionEvents() as $compEvent) {
                $wcaEventID = $compEvent->Event()->WCAEventID;
                $wcaExportColumns[$wcaEventID] = $wcaEventID;
            }

            $gridField->getConfig()
                ->removeComponentsByType(GridFieldAddNewButton::class)
                ->removeComponentsByType(GridFieldAddExistingAutocompleter::class)
                ->addComponent(new GridFieldExportButton('buttons-before-left', $wcaExportColumns));
        }

        return $fields;
    }

    public function HasEventFees()
    {
        if ($events = $this->CompetitionEvents()->filter(['FeeAmount:GreaterThan' => '0'])) {
            return true;
        }
        return false;
    }

    public function IsRegistrationOpen()
    {
        $now = DBDatetime::now()->getTimestamp();
        $open = DBDatetime::create()->setValue($this->RegistrationOpen)->getTimestamp();
        $close = DBDatetime::create()->setValue($this->RegistrationClose)->getTimestamp();
        return ($open <= $now && $now < $close);
    }

    public function IsRegistrationClosed()
    {
        $now = DBDatetime::now()->getTimestamp();
        $close = DBDatetime::create()->setValue($this->RegistrationClose)->getTimestamp();
        return $close <= $now;
    }
    
    public function Link($action = null)
    {
        return CompetitionsPage::get()->first()->Link($action ? $this->WCAID . '/' . $action : $this->WCAID);
    }
}
