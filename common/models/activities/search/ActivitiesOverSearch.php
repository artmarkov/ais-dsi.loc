<?php

namespace common\models\activities\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activities\ActivitiesOver;

/**
 * ActivitiesOverSearch represents the model behind the search form about `common\models\activities\ActivitiesOver`.
 */
class ActivitiesOverSearch extends ActivitiesOver
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'over_category', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['title', 'department_list', 'executors_list', 'description', 'datetime_in', /*'datetime_out'*/], 'safe'],
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
        $query = ActivitiesOver::find();

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
            'over_category' => $this->over_category,
//            'datetime_in' => $this->datetime_in,
//            'datetime_out' => $this->datetime_out,
            'auditory_id' => $this->auditory_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        if ($this->datetime_in) {
            $tmp = explode(' - ', $this->datetime_in);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.datetime_in',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'department_list', $this->department_list])
            ->andFilterWhere(['like', 'executors_list', $this->executors_list])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
