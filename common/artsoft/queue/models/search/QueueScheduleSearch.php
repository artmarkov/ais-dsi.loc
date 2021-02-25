<?php

namespace artsoft\queue\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use artsoft\queue\models\QueueSchedule;

/**
 * QueueScheduleSearch represents the model behind the search form about `artsoft\queue\models\QueueSchedule`.
 */
class QueueScheduleSearch extends QueueSchedule
{
    public $next_date_operand;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priority', 'created_at', 'updated_at', 'created_by', 'updated_by', 'status'], 'integer'],
            [['title', 'content', 'next_date', 'class', 'cron_expression', 'next_date_operand'], 'safe'],
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
        $query = QueueSchedule::find();

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
            'status' => $this->status,
            'priority' => $this->priority,
//            'next_date' => $this->next_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        
        switch ($this->next_date_operand) {
            case '=':
                $query->andFilterWhere(['>=', 'next_date', ($this->next_date) ? strtotime($this->next_date) : null]);
                $query->andFilterWhere(['<=', 'next_date', ($this->next_date) ? strtotime($this->next_date . ' 23:59:59') : null]);
                break;
            case '>':
                $query->andFilterWhere(['>', 'next_date', ($this->next_date) ? strtotime($this->next_date . ' 23:59:59') : null]);
                break;
            case '<':
                $query->andFilterWhere(['<', 'next_date', ($this->next_date) ? strtotime($this->next_date) : null]);
                break;
            default:
                break;
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'cron_expression', $this->cron_expression]);

        return $dataProvider;
    }
}
