<?php

namespace common\models\service\search;

use common\models\service\UsersAttendlogView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\service\UsersAttendlog;

/**
 * UsersAttendlogSearch represents the model behind the search form about `common\models\service\UsersAttendlogView`.
 */
class UsersAttendlogViewSearch extends UsersAttendlogView
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: UsersAttendlogView::find();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'auditory_id', 'timestamp_received', 'timestamp_over', 'timestamp'], 'integer'],
            [['user_category', 'user_category_name', 'user_name', 'timestamp'], 'string'],
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
                    'timestamp' => SORT_DESC,
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
            'auditory_id' => $this->auditory_id,
            'timestamp_received' => $this->timestamp_received,
            'timestamp_over' => $this->timestamp_over,
            'timestamp' => $this->timestamp

        ]);
        $query->andFilterWhere(['like', 'user_category', $this->user_category])
            ->andFilterWhere(['like', 'user_category_name', $this->user_category_name])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);
        return $dataProvider;
    }
}
