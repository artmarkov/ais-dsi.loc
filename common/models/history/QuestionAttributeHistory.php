<?php

namespace common\models\history;

use common\models\question\QuestionAttribute;
use common\widgets\history\BaseHistory;

class QuestionAttributeHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'question_attribute_hist';
    }

    public static function getModelName()
    {
        return QuestionAttribute::class;
    }

    protected function getFields()
    {
        return [
            'type_id',
            'label',
            'hint',
            'required',
            'description',
            'default_value',
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
            case 'type_id':
                return isset($model->type_id) ? $model::getTypeValue($value) : $value;
            case 'required':
                return isset($model->required) ? ($model->required ? 'Да' : 'Нет') : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (QuestionOptionsHistory::getLinkedIdList('attribute_id', $this->objId) as $atrId) {
            $vf = new QuestionOptionsHistory($atrId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}