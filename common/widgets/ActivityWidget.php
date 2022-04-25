<?php

namespace common\widgets;

use artsoft\helpers\Schedule;
use common\models\schedule\SubjectScheduleView;
use common\models\teachers\TeachersLoad;
use common\models\user\UserCommon;
use yii\data\ActiveDataProvider;

class ActivityWidget extends \yii\bootstrap\Widget
{
    public $user_common_id;
    public $timestamp;

    public function run()
    {
        $week_day = Schedule::timestamp2WeekDay($this->timestamp);
        $week_num = Schedule::timestamp2WeekNum($this->timestamp);
        $model = UserCommon::findOne($this->user_common_id);
        if ($model->user_category == 'teachers') {
            $query = SubjectScheduleView::find()
                ->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($model->getRelatedId())])
                ->andWhere(['is not', 'subject_schedule_id', null])
                ->andWhere(['=', 'week_day', $week_day])
                ->andWhere(new \yii\db\Expression('CASE WHEN week_num IS NOT NULL THEN week_num = :week_num ELSE TRUE END', [':week_num' => $week_num]));
            $dataProvider = new ActiveDataProvider(['query' => $query, 'sort' => false, 'pagination' => false]);
            return $this->render('activityWidget', [
                'dataProvider' => $dataProvider]);
        }
        return false;
    }
}
