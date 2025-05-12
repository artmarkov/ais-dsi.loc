<?php

namespace common\models\planfix\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\planfix\Planfix;

/**
 * PlanfixSearch represents the model behind the search form about `common\models\planfix\Planfix`.
 */
class PlanfixSearch extends Planfix
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'planfix_author', 'importance', 'planfix_date'], 'integer'],
            [['name', 'description', 'executors_list', 'status_reason'], 'safe'],
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
        $query = Planfix::find();

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
            'category_id' => $this->category_id,
            'planfix_author' => $this->planfix_author,
            'importance' => $this->importance,
            'planfix_date' => $this->planfix_date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'executors_list', $this->executors_list])
            ->andFilterWhere(['like', 'status_reason', $this->status_reason]);

        return $dataProvider;
    }
}
