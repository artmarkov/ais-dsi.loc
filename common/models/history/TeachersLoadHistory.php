<?php
namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\teachers\TeachersLoad;
use common\widgets\history\BaseHistory;

class TeachersLoadHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_load_hist';
    }

    public static function getModelName()
    {
        return TeachersLoad::class;
    }

    protected function getFields()
    {
        return [
            'direction_id',
            'direction_vid_id',
            'teachers_id',
            'load_time',
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
            case 'direction_id':
                return isset($model->direction_id) ? $model->direction->name : $value;
            case 'direction_vid_id':
                return isset($model->direction_vid_id) ? $model->directionVid->name : $value;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
            case 'load_time':
                return isset($model->load_time) ? $model->load_time . ' [' . RefBook::find('teachers_fio')->getValue($model->teachers_id) . '-' . $model->direction->slug . ']' : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

}