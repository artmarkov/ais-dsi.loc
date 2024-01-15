<?php

namespace artsoft\user\widgets\dashboard;

use artsoft\models\Session;
use artsoft\widgets\DashboardWidget;

class UsersActive extends DashboardWidget
{
    public $activeTime = 10800; //время нахождения на сайте 3 часа
    public $category = ['employees', 'teachers'];

    public function run()
    {
        $active = Session::find()
            ->select(['CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as fullname'])
            ->distinct('fullname')
            ->innerJoin('users', 'users.id = session.user_id')
            ->innerJoin('user_common', "user_common.user_id = users.id")
            ->where(['in', 'user_common.user_category', $this->category])
            ->andWhere(['>', 'run_at', time() - $this->activeTime])
            ->column();

        return $this->render('users-active', [
            'active' => $active,
            'activeTime' => $this->activeTime,
        ]);
    }

}