<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;

class CompetitionEvent extends DataObject
{
    private static $db = [
        'Fee' => 'Money',
    ];

    private static $has_one = [
        'Competition' => Competition::class,
        'Event' => Event::class,
    ];

    private static $belongs_many_many = [
        'Registrations' => Registration::class,
    ];

    private static $summary_fields = [
        'Event.Name' => 'Event',
        'Fee' => 'Registration Fee',
    ];

    public function populateDefaults() 
    {
        $this->Fee = DBMoney::create()->setCurrency('NZD');
        parent::populateDefaults();
    }

    public function getName()
    {
        return $this->Event()->Name;
    }
}
