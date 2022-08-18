<?php

namespace common\models\entrant\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\entrant\GuideEntrantTest;

/**
 * GuideEntrantTestSearch represents the model behind the search form about `common\models\entrant\GuideEntrantTest`.
 */
class GuideEntrantTestSearch extends GuideEntrantTest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'division_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'name_dev'], 'safe'],
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
        $query = GuideEntrantTest::find();

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
            'division_id' => $this->division_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_dev', $this->name_dev]);

        return $dataProvider;
    }
}
