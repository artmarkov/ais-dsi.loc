<?php

namespace frontend\widgets\dashboard;

use artsoft\widgets\DashboardWidget;

class Quick extends DashboardWidget
{
    public function run()
    {
        return $this->render('quick');
    }
}