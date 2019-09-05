<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

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
    ];

    public function canView($member = null) 
    {
        return true;
    }
}
