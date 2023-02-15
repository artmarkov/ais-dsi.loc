<?php

namespace common\models\schoolplan\search;

use common\models\schoolplan\SchoolplanView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SchoolplanSearch represents the model behind the search form about `common\models\schoolplan\SchoolplanView`.
 */
class SchoolplanViewSearch extends SchoolplanView
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: SchoolplanView::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'datetime_in', 'datetime_out', 'auditory_id', 'category_id', 'form_partic', 'visit_poss', 'important_event', 'num_users', 'num_winners', 'num_visitors', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'author_id', 'doc_status'], 'integer'],
            [['title', 'places', 'department_list', 'executors_list', 'partic_price', 'visit_content', 'region_partners', 'site_url', 'site_media', 'description', 'rider', 'result', 'auditory_places'], 'safe'],
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
            'author_id' => $this->author_id,
            'datetime_in' => $this->datetime_in,
            'datetime_out' => $this->datetime_out,
            'auditory_id' => $this->auditory_id,
            'category_id' => $this->category_id,
            'form_partic' => $this->form_partic,
            'visit_poss' => $this->visit_poss,
            'important_event' => $this->important_event,
            'num_users' => $this->num_users,
            'num_winners' => $this->num_winners,
            'num_visitors' => $this->num_visitors,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
            'doc_status' => $this->doc_status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'places', $this->places])
            ->andFilterWhere(['like', 'department_list', $this->department_list])
            ->andFilterWhere(['like', 'executors_list', $this->executors_list])
            ->andFilterWhere(['like', 'partic_price', $this->partic_price])
            ->andFilterWhere(['like', 'visit_content', $this->visit_content])
            ->andFilterWhere(['like', 'region_partners', $this->region_partners])
            ->andFilterWhere(['like', 'site_url', $this->site_url])
            ->andFilterWhere(['like', 'site_media', $this->site_media])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rider', $this->rider])
            ->andFilterWhere(['like', 'result', $this->result])
            ->andFilterWhere(['like', 'auditory_places', $this->auditory_places]);

        return $dataProvider;
    }
}
