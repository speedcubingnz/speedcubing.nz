<?php

namespace App\Model;

use App\Model\CompetitionsPage;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;

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
        'BaseFee' => 'Currency',
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
    
    public function Link()
    {
        return CompetitionsPage::get()->first()->Link($this->WCAID);
    }
}
