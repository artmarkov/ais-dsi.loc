<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\LessonTest;

/**
 * LessonTestSearch represents the model behind the search form about `common\models\education\LessonTest`.
 */
class LessonTestSearch extends LessonTest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'test_category', 'plan_flag', 'status', 'sort_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['division_list', 'test_name', 'test_name_short'], 'safe'],
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
        $query = LessonTest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
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
            'test_category' => $this->test_category,
            'plan_flag' => $this->plan_flag,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'division_list', $this->division_list])
            ->andFilterWhere(['like', 'test_name', $this->test_name])
            ->andFilterWhere(['like', 'test_name_short', $this->test_name_short]);

        return $dataProvider;
    }
}
