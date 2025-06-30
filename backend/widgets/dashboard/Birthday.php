<?php

namespace backend\widgets\dashboard;

use artsoft\widgets\DashboardWidget;
use common\models\user\UserCommon;

class Birthday extends DashboardWidget
{
    public function run()
    {
        $models = UserCommon::getUsersBirthdayByCategory(['employees', 'teachers']);

        return $this->render('birthday', [
            'models' => $models,
        ]);
    }
}