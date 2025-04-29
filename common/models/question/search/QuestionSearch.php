<?php

namespace common\models\question\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\question\Question;

/**
 * QuestionSearch represents the model behind the search form about `common\models\question\Question`.
 */
class QuestionSearch extends Question
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'author_id', 'name', 'category_id', 'vid_id', 'timestamp_in', 'timestamp_out', 'status', 'email_flag', 'email_author_flag', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['division_list', 'description', 'users_cat'], 'safe'],
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
        $query = Question::find();

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
            'name' => $this->name,
            'category_id' => $this->category_id,
            'vid_id' => $this->vid_id,
            'timestamp_in' => $this->timestamp_in,
            'timestamp_out' => $this->timestamp_out,
            'status' => $this->status,
            'email_flag' => $this->email_flag,
            'email_author_flag' => $this->email_author_flag,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'division_list', $this->division_list])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'users_cat', $this->users_cat]);

        return $dataProvider;
    }
}
