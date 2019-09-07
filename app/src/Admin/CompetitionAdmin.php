<?php

namespace App\Admin;

use App\Model\Competition;
use SilverStripe\Admin\ModelAdmin;

class CompetitionAdmin extends ModelAdmin
{
    private static $menu_title = 'Competitions';

    private static $url_segment = 'competitions';

    private static $managed_models = [
        Competition::class,
    ];

    private static $summary_fields = [
        'WCAID' => 'WCA Competition ID',
    ];
}
