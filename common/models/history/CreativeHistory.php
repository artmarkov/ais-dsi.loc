<?php

namespace common\models\history;

use common\models\creative\CreativeWorks;
use common\models\own\Department;
use common\models\teachers\Teachers;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class CreativeHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'creative_works_hist';
    }

    public static function getModelName()
    {
        return CreativeWorks::class;
    }

    protected function getFields()
    {
        return [
            'category_id',
            'name',
            'description',
            'department_list',
            'teachers_list',
            'published_at',
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
            case 'teachers_list':
                if (isset($model->teachers_list)) {
                    $v = [];
                    foreach (Json::decode($model->teachers_list) as $id) {
                        $v[] = $id != null ? Teachers::findOne($id)->getFullName() : null;
                    }
                    return implode(', ', $v);
                }
            case 'department_list':
                if (isset($model->department_list)) {
                    $v = [];
                    foreach (Json::decode($model->department_list) as $id) {
                        $v[] = $id != null ? Department::findOne($id)->name : null;
                    }
                    return implode(', ', $v);
                }
            case 'category_id':
                return isset($model->category_id) ? $model->category->name : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (EfficiencyHistory::getLinkedIdList('item_id', $this->objId) as $efficiencyId) {
            $vf = new EfficiencyHistory($efficiencyId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }

    /**
     * @param $linkFiledName
     * @param $id
     * @return array|mixed
     */
    public static function getLinkedIdList($linkFiledName, $id)
    {
        $res = array_reduce((new \yii\db\Query)->select('id')->distinct()->from(static::getTableName())
            ->where([$linkFiledName => $id])
            ->andWhere(['class' => \yii\helpers\StringHelper::basename(self::getModelName())])
            ->all(), function ($result, $item) {
            $result[] = $item['id'];
            return $result;
        });
        return $res ? $res : [];
    }
}