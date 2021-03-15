<?php

namespace artsoft\eav;

use yii\db\Query;

/**
 * EAV query trait.
 * Modify ActiveRecord query for EAV support
 */
trait EavQueryTrait
{
    private $_eavJoinIndex = 0;

    public function andEavWhere($condition, $name, $value)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $categoryField = $modelClass::getEavCategoryField();

        $query = $this->getEavJoinQuery()
            ->where('{{%eav_attribute}}.name = :name AND {{%eav_value}}.value LIKE :value', [
                ':name' => $name,
                ':value' => "%$value%",
            ])->createCommand()->getRawSql();

        $this->_eavJoinIndex++;
        $joinID = $this->_eavJoinIndex;

        /* @var \yii\db\ActiveQuery */
        $this->innerJoin("({$query}) eavjt{$joinID}", "eavjt{$joinID}.item_id = $tableName.id AND eavjt{$joinID}.category_id = $tableName.$categoryField");

        return $this;
    }

    public function andEavFilterWhere($condition, $name, $value)
    {
        if (!empty($value)) {
            $this->andEavWhere($condition, $name, $value);
        }
        return $this;
    }

    protected function getEavJoinQuery()
    {
        return (new Query())->from('{{%eav_value}}')
            ->select(['{{%eav_value}}.item_id', '{{%eav_entity}}.category_id', '{{%eav_attribute}}.name', '{{%eav_value}}.value'])
            ->innerJoin('{{%eav_entity}}', '{{%eav_value}}.entity_id = {{%eav_entity}}.id')
            ->innerJoin('{{%eav_entity_model}}', '{{%eav_entity_model}}.id = {{%eav_entity}}.model_id AND {{%eav_entity_model}}.entity_model = :entity_model', [':entity_model' => $this->modelClass])
            ->innerJoin('{{%eav_attribute}}', '{{%eav_attribute}}.id = {{%eav_value}}.attribute_id');
    }
}