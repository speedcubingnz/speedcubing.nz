<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

class Event extends DataObject
{
    private static $db = [
        'WCAEventID' => 'Varchar',
        'Name' => 'Varchar',
        'Rank' => 'Int',
    ];

    private static $belongs_many_many = [
        'Competitions' => Competition::class,
    ];
}
