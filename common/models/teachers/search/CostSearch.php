<?php

namespace common\models\teachers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\Cost;

/**
 * CostSearch represents the model behind the search form about `common\models\teachers\Cost`.
 */
class CostSearch extends Cost
{
    public $directionName;
    public $stakeName;
    public $stakeSlug;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'direction_id', 'stake_id'], 'safe'],
            [['stake_value'], 'number'],
            [['directionName','stakeName','stakeSlug'], 'string'],
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
        $query = Cost::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'direction_id',
                'stake_id',
                'stake_value',
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
        $query->joinWith(['direction']);
        $query->joinWith(['stake']);

        $query->andFilterWhere([
            'stake_value' => $this->stake_value,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'direction_id', $this->direction_id])
            ->andFilterWhere(['like', 'stake_id', $this->stake_id]);

        return $dataProvider;
    }
}
