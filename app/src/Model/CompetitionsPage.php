<?php

namespace App\Model;

use Page;
use App\Controller\CompetitionsPageController;

class CompetitionsPage extends Page
{
    private static $controller_name = CompetitionsPageController::class;

    private static $table_name = 'CompetitionsPage';
}