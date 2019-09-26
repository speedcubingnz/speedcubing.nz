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

    public function __construct($record = null, $isSingleton = false, $queryParams = array())
    {
        parent::__construct($record, $isSingleton, $queryParams);

        // Add dynamic registered event properties for CSV export
        if ($competition = $this->Competition()) {
            $competitionEvents = $competition->CompetitionEvents();
            foreach ($competitionEvents as $competitionEvent) {
                $event = $competitionEvent->Event();
                $this->{$event->WCAEventID} = ($this->IsDoing($event->WCAEventID) ? '1' : '0');
            }
        }
    }

    public function WCAStatus()
    {
        if ($this->Payments()->filter(['Status' => 'Captured'])->first()) {
            return 'a';
        } else {
            return 'p';
        }
    }

    public function IsDoing(string $event)
    {
        if ($this->CompetitionEvents()->filter(['Event.WCAEventID' => $event])->count() > 0) {
            return true;
        } 
        return false;
    }
}
