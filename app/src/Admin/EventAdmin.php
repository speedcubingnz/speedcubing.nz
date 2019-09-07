<?php

namespace App\Admin;

use App\Model\Event;
use SilverStripe\Admin\ModelAdmin;

class EventAdmin extends ModelAdmin
{
    private static $menu_title = 'Events';

    private static $url_segment = 'events';

    private static $managed_models = [
        Event::class,
    ];

    private static $summary_fields = [
        'Name' => 'Event',
    ];
}
