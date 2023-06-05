<?php

namespace common\models\entrant\search;

use artsoft\traits\ParamsTrimable;
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
    use ParamsTrimable;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'comm_id', 'group_id', 'decision_id', 'programm_id', 'subject_id', 'course', 'subject_form_id', 'status', 'timestamp_in', 'birth_date'], 'integer'],
            [['last_experience', 'reason', 'subject_list', 'group_name'], 'safe'],
            [['mid_mark', 'fullname', 'fio'], 'safe'],
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
                    'timestamp_in' => SORT_ASC,
                    'fullname' => SORT_ASC
                ],
            ],
        ]);
        $params = $this->trimParams($params, static::class);
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
            'programm_id' => $this->programm_id,
            'subject_id' => $this->subject_id,
            'course' => $this->course,
            'subject_form_id' => $this->subject_form_id,
            'status' => $this->status,
            'mid_mark' => $this->mid_mark,
            'timestamp_in' => $this->timestamp_in,
            'birth_date' => $this->birth_date,
        ]);

        $query->andFilterWhere(['like', 'last_experience', $this->last_experience])
            ->andFilterWhere(['like', 'subject_list', $this->subject_list])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'group_name', $this->group_name])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'fio', $this->fio]);

        return $dataProvider;
    }
}
