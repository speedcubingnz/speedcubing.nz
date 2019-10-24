<?php

namespace App\Admin;

use App\Model\Country;
use SilverStripe\Admin\ModelAdmin;

class CountryAdmin extends ModelAdmin
{
    private static $menu_title = 'Countries';

    private static $url_segment = 'countries';

    private static $managed_models = [
        Country::class,
    ];
}
