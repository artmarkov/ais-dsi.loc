<?php

namespace common\models\entrant\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\entrant\Entrant;

/**
 * EntrantSearch represents the model behind the search form about `common\models\entrant\Entrant`.
 */
class EntrantSearch extends Entrant
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'comm_id', 'group_id', 'decision_id', 'unit_reason_id', 'plan_id', 'course', 'type_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['last_experience', 'remark', 'reason', 'subject_list'], 'safe'],
            [['mid_mark'], 'number'],
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
        $query = Entrant::find();

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
            'mid_mark' => $this->mid_mark,
            'unit_reason_id' => $this->unit_reason_id,
            'plan_id' => $this->plan_id,
            'course' => $this->course,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'last_experience', $this->last_experience])
            ->andFilterWhere(['like', 'subject_list', $this->subject_list])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
