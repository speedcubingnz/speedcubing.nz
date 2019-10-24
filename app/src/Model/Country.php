<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

class Country extends DataObject
{
    private static $db = [
        'WCAID' => 'Varchar',
        'Name' => 'Varchar',
        'WCAContinentID' => 'Varchar',
        'ISO2' => 'Varchar(2)',
    ];
}