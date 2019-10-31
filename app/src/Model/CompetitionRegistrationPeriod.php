<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

class CompetitionRegistrationPeriod extends DataObject
{
    private static $db = [
        'StartDate' => 'Datetime',
        'EndDate' => 'Datetime',
        'BaseFee' => 'Money',
    ];

    private static $has_one = [
        'Competition' => Competition::class,
    ];
}
