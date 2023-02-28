<?php

namespace common\models\activities\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activities\Activities;

/**
 * ActivitiesSearch represents the model behind the search form about `common\models\activities\Activities`.
 */
class ActivitiesSearch extends Activities
{
    public $start_time_operand;
    public $end_time_operand;

    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: Activities::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'auditory_id'], 'integer'],
            [['resource', 'title', 'description', 'all_day'], 'safe'],
            [['start_time'], 'safe'],
           // [['start_time_operand'], 'string'],
           // [['end_time_operand'], 'string'],
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
            'sort' => false,
            
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
            'auditory_id' => $this->auditory_id,
            'all_day' => $this->all_day,
        ]);
        if ($this->start_time) {
            $tmp = explode(' - ', $this->start_time);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.start_time',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

//        $query->andFilterWhere([($this->start_time_operand) ? $this->start_time_operand : '=', 'start_time', ($this->start_time) ? strtotime($this->start_time) : null]);
        //$query->andFilterWhere([($this->end_time_operand) ? $this->end_time_operand : '=', 'end_time', ($this->end_time) ? strtotime($this->end_time) : null]);

        return $dataProvider;
    }
}
