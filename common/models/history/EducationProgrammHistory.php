<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\education\EducationProgramm;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class EducationProgrammHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'education_programm_hist';
    }

    public static function getModelName()
    {
        return EducationProgramm::class;
    }

    protected function getFields()
    {
        return [
            'education_cat_id',
            'name',
            'speciality_list',
            'description',
            'status',
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
            case 'speciality_list':
                if (isset($model->speciality_list)) {
                    $v = [];
                    foreach (Json::decode($model->speciality_list) as $id) {
                        $v[] = $id != null ? RefBook::find('education_speciality')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }

            case 'education_cat_id':
                return isset($model->education_cat_id) ? RefBook::find('education_cat')->getValue($value) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (EducationProgrammLevelHistory::getLinkedIdList('programm_id', $this->objId) as $subjectId) {
            $vf = new EducationProgrammLevelHistory($subjectId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}