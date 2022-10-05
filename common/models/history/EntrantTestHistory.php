<?php

namespace common\models\history;

use common\models\education\LessonMark;
use common\models\entrant\EntrantMembers;
use common\models\entrant\EntrantTest;
use common\models\entrant\GuideEntrantTest;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;

class EntrantTestHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_test_hist';
    }

    public static function getModelName()
    {
        return EntrantTest::class;
    }

    protected function getFields()
    {
        return [
            'entrant_members_id',
            'entrant_test_id',
            'entrant_mark_id',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return int|string|null
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'entrant_mark_id':
                return isset($model->entrant_mark_id) ? LessonMark::findOne($model->entrant_mark_id)->mark_label : $value;
            case 'entrant_test_id':
                return isset($model->entrant_test_id) ? GuideEntrantTest::findOne($model->entrant_test_id)->name : $value;
            case 'entrant_members_id':
                $members_id = isset($model->entrant_members_id) ? EntrantMembers::findOne($model->entrant_members_id)->members_id : $value;
                return UserCommon::findOne(['user_id' => $members_id]) ? UserCommon::findOne(['user_id' => $members_id])->getLastFM() : $members_id;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}