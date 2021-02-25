<?php

namespace artsoft\widgets\dashboard;

use artsoft\widgets\DashboardWidget;

class Info extends DashboardWidget
{
    public function run()
    {
        return $this->render('info');
    }
}