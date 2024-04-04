<?php

namespace common\models\service\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\service\WorkingTimeLog;

/**
 * searchWorkingTimeLogSearch represents the model behind the search form about `common\models\service\WorkingTimeLog`.
 */
class WorkingTimeLogSearch extends WorkingTimeLog
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: WorkingTimeLog::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'timestamp_work_in', 'timestamp_work_out', 'timestamp_activities_in', 'timestamp_activities_out'], 'integer'],
            [['date', 'comment'], 'safe'],
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
        $query = $this->query;

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
            'user_common_id' => $this->user_common_id,
            'date' => $this->date,
            'timestamp_work_in' => $this->timestamp_work_in,
            'timestamp_work_out' => $this->timestamp_work_out,
            'timestamp_activities_in' => $this->timestamp_activities_in,
            'timestamp_activities_out' => $this->timestamp_activities_out,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
