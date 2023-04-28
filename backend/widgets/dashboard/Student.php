<?php

namespace backend\widgets\dashboard;

use artsoft\widgets\DashboardWidget;

class Student extends DashboardWidget
{
    public function run()
    {
        return $this->render('student');
    }
}