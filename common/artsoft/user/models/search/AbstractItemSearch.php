<?php

namespace artsoft\user\models\search;

use artsoft\models\AbstractItem;
use artsoft\models\Permission;
use artsoft\models\Role;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

abstract class AbstractItemSearch extends AbstractItem
{

    public function rules()
    {
        return [
            [['name', 'description', 'group_code'], 'string'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = (static::ITEM_TYPE == static::TYPE_ROLE) ? Role::find() : Permission::find();

        $query->joinWith(['group']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', Yii::$app->art->auth_item_table . '.name', $this->name])
            ->andFilterWhere(['like', Yii::$app->art->auth_item_table . '.description', $this->description])
            ->andFilterWhere([Yii::$app->art->auth_item_table . '.group_code' => $this->group_code]);

        return $dataProvider;
    }
}