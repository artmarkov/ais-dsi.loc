<?php

namespace common\models\entrant\search;

use common\models\entrant\EntrantView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\entrant\Entrant;

/**
 * EntrantSearch represents the model behind the search form about `common\models\entrant\EntrantView`.
 */
class EntrantSearch extends EntrantView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'comm_id', 'group_id', 'decision_id', 'speciality_id', 'programm_id', 'course', 'type_id', 'status'], 'integer'],
            [['last_experience', 'reason', 'subject_list'], 'safe'],
            [['mid_mark'], 'safe'],
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
        $query = EntrantView::find();

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
            'student_id' => $this->student_id,
            'comm_id' => $this->comm_id,
            'group_id' => $this->group_id,
            'decision_id' => $this->decision_id,
            'speciality_id' => $this->speciality_id,
            'programm_id' => $this->programm_id,
            'course' => $this->course,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'mid_mark' => $this->mid_mark,
        ]);

        $query->andFilterWhere(['like', 'last_experience', $this->last_experience])
            ->andFilterWhere(['like', 'subject_list', $this->subject_list])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
