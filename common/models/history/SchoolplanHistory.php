<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\own\Department;
use common\models\schoolplan\Schoolplan;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class SchoolplanHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'schoolplan_hist';
    }

    public static function getModelName()
    {
        return Schoolplan::class;
    }

    protected function getFields()
    {
        return [
            'title',
            'datetime_in',
            'datetime_out',
            'places',
            'auditory_id',
            'department_list',
            'executors_list',
            'category_id',
            'form_partic',
            'partic_price',
            'visit_poss',
            'visit_content',
            'important_event',
            'format_event',
            'region_partners',
            'site_url',
            'site_media',
            'description',
            'rider',
            'result',
            'num_users',
            'num_winners',
            'num_visitors',
            'bars_flag',
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
            case 'department_list':
                if (isset($model->department_list)) {
                    $v = [];
                    foreach (Json::decode($model->department_list) as $id) {
                        $v[] = $id != null ? Department::findOne($id)->name : null;
                    }
                    return implode(', ', $v);
                }
                break;
            case 'executors_list':
                if (isset($model->executors_list)) {
                    $v = [];
                    foreach (Json::decode($model->executors_list) as $id) {
                        $v[] = $id != null ? UserCommon::findOne($id)->getFullName() : null;
                    }
                    return implode(', ', $v);
                }
                break;
            case 'auditory_id':
                return isset($model->auditory_id) ? RefBook::find('auditory_memo_1')->getValue($model->auditory_id) : $value;
                break;
            case 'category_id':
                return isset($model->category_id) ? $model->getCategoryName() : $value;
                break;
            case 'form_partic':
                return isset($model->form_partic) ? $model->getFormParticValue($model->form_partic) : $value;
                break;
            case 'visit_poss':
                return isset($model->visit_poss) ? $model->getVisitPossValue($model->visit_poss) : $value;
                break;
            case 'important_event':
                return isset($model->important_event) ? $model->getImportantValue($model->important_event) : $value;
                break;
            case 'format_event':
                return isset($model->format_event) ? $model->getFormatValue($model->format_event) : $value;
                break;
            case 'bars_flag':
                return isset($model->bars_flag) ? ($model->bars_flag ? 'Да' : 'Нет') : $value;
                break;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        $modelDependency = $this->getModelName()::findOne($this->objId)->activitiesOver;
        if($modelDependency) {
            $id = $modelDependency->id;
            $vf = new ActivitiesOverHistory($id);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}