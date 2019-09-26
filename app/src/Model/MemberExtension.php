<?php

namespace App\Model;

use SilverStripe\ORM\DataExtension;

class MemberExtension extends DataExtension
{
    private static $summary_fields = array(
        'Name' => 'Name',
        'WCAID' => 'WCA ID',
        'Email' => 'Email',
    );

    private static $db = [
        'Name' => 'Varchar',
        'WCAID' => 'Varchar(10)',
        'Birthdate' => 'Date',
        'Gender' => 'Varchar(1)',
        'CountryISO2' => 'Varchar(2)',
        'AcceptsMarketing' => 'Boolean',
    ];

    private static $has_many = [
        'Registrations' => Registration::class,
    ];
}
