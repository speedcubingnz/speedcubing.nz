<?php

namespace App\Controller;

use App\Controller\PageController;
use App\Model\Competition;

class CompetitionsPageController extends PageController
{
    private static $allowed_actions = ['competition'];

    private static $url_handlers = [
        '$WCAID' => 'competition',
    ];

    public function competition()
    {
        $WCAID = $this->getRequest()->param('WCAID');

        $competition = Competition::get()->filter(['WCAID' => $WCAID])->first();

        return $this->customise(['Competition' => $competition])->renderWith(['Competition', 'Page']);
    }
}