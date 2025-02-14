<?php

namespace common\models\creative\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\creative\CreativeWorks;

/**
 * CreativeWorksSearch represents the model behind the search form about `common\models\creative\CreativeWorks`.
 */
class CreativeWorksSearch extends CreativeWorks
{
    public $published_at_operand;
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: CreativeWorks::find();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['department_list', 'teachers_list', 'category_id', 'name', 'description', 'published_at', 'created_at', 'updated_at'], 'safe'],
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
//        жадная загрузка
        $query->joinWith(['category']);

        $query->andFilterWhere([
            'creative_works.id' => $this->id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'created_at' => ($this->created_at) ? strtotime($this->created_at) : null,
            ]);

        $query->andFilterWhere([($this->published_at_operand) ? $this->published_at_operand : '=', 'published_at', ($this->published_at) ? strtotime($this->published_at) : null]);

        $query->andFilterWhere(['like', 'creative_works.name', $this->name])
            ->andFilterWhere(['like', 'department_list', $this->department_list])
            ->andFilterWhere(['like', 'teachers_list', $this->teachers_list])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
