<?php

namespace artsoft\eav\models\search;

use artsoft\eav\models\EavAttributeType;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EavAttributeTypeSearch represents the model behind the search form about `artsoft\eav\models\EavAttributeType`.
 */
class EavAttributeTypeSearch extends EavAttributeType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'store_type'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EavAttributeType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'store_type' => $this->store_type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}