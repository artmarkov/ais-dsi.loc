<?php

namespace common\models\studyplan\search;

use common\models\studyplan\SubjectCharacteristicView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubjectCharacteristicViewSearch represents the model behind the search form about `common\models\studyplan\SubjectCharacteristicView`.
 */
class SubjectCharacteristicViewSearch extends SubjectCharacteristicView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'student_id', 'plan_year', 'programm_id', 'speciality_id', 'course', 'status', 'studyplan_subject_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'subject_characteristic_id'], 'integer'],
            [['description'], 'safe'],
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
        $query = SubjectCharacteristicView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'sort' => [
                'defaultOrder' => false,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'programm_id' => $this->programm_id,
            'speciality_id' => $this->speciality_id,
            'course' => $this->course,
            'status' => $this->status,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'subject_characteristic_id' => $this->subject_characteristic_id
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
