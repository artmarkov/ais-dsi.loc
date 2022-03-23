<?php
namespace common\models\history;

use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\widgets\history\BaseHistory;

class LessonItemsHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'lesson_items_hist';
    }

    public static function getModelName()
    {
        return LessonItems::class;
    }

    protected function getFields()
    {
        return [
            'lesson_test_id',
            'lesson_date',
            'lesson_topic',
            'lesson_rem',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'lesson_test_id':
                return isset($model->lesson_test_id) ? $model->lessonTest->test_name : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (LessonItemsProgressHistory::getLinkedIdList('lesson_items_id', $this->objId) as $lessonProgressId) {
            $vf = new LessonItemsProgressHistory($lessonProgressId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}