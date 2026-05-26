<?php

namespace common\models\schoolplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\schoolplan\SchoolplanActivity;

/**
 * SchoolplanActivitySearch represents the model behind the search form about `common\models\schoolplan\SchoolplanActivity`.
 */
class SchoolplanActivitySearch extends SchoolplanActivity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schoolplan_id', 'author_id', 'executor_id', 'datetime_in', 'activity_status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['name', 'places', 'author_comment', 'executor_comment', 'activity_status_reason'], 'safe'],
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
        $query = SchoolplanActivity::find();

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
            'schoolplan_id' => $this->schoolplan_id,
            'author_id' => $this->author_id,
            'executor_id' => $this->executor_id,
            'datetime_in' => $this->datetime_in,
            'activity_status' => $this->activity_status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'places', $this->places])
            ->andFilterWhere(['like', 'author_comment', $this->author_comment])
            ->andFilterWhere(['like', 'executor_comment', $this->executor_comment])
            ->andFilterWhere(['like', 'activity_status_reason', $this->activity_status_reason]);

        return $dataProvider;
    }
}
