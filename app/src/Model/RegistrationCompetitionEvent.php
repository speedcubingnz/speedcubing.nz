<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

class RegistrationCompetitionEvent extends DataObject
{
    private static $db = [
    ];

    private static $has_one = [
        'CompetitionEvent' => CompetitionEvent::class,
        'Registration' => Registration::class,
    ];
}
