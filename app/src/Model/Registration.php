<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

class Registration extends DataObject
{
    private static $extensions = [
        Versioned::class . '.versioned',
    ];

    private static $db = [
        'Comments' => 'Text',
    ];

    private static $has_one = [
        'Member' => Member::class,
        'Competition' => Competition::class,
    ];

    private static $many_many = [
        'CompetitionEvents' => [
            'through' => RegistrationCompetitionEvent::class,
            'from' => 'Registration',
            'to' => 'CompetitionEvent',
        ]
    ];

    private static $summary_fields = [
        'Member.Name' => 'Name',
    ];

    /*private static $has_many = [
        'Payments' => 'Payment',
    ];*/
}
