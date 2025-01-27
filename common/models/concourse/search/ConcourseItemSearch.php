<?php

namespace common\models\concourse\search;

use common\models\concourse\ConcourseItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * QuestionAttributeSearch represents the model behind the search form about `common\models\concourse\ConcourseItem`.
 */
class ConcourseItemSearch extends ConcourseItem
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: ConcourseItem::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'concourse_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['authors_list', 'description', 'name'], 'safe'],
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
                    'id' => SORT_ASC,
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
            'concourse_id' => $this->concourse_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'authors_list', $this->authors_list])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
