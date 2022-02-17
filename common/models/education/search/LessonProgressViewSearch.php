<?php

namespace common\models\education\search;

use common\models\education\LessonProgressView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LessonProgressSearch represents the model behind the search form about `common\models\education\LessonProgressView`.
 */
class LessonProgressViewSearch extends LessonProgressView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'student_id', 'plan_year', 'programm_id', 'speciality_id', 'course', 'status', 'studyplan_subject_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'subject_sect_studyplan_id', 'lesson_qty', 'current_qty', 'absence_qty'], 'integer'],
            [['current_avg_mark', 'middle_avg_mark', 'finish_avg_mark'], 'number'],
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
        $query = LessonProgressView::find();

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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'lesson_qty' => $this->lesson_qty,
            'current_qty' => $this->current_qty,
            'absence_qty' => $this->absence_qty,
            'current_avg_mark' => $this->current_avg_mark,
            'middle_avg_mark' => $this->middle_avg_mark,
            'finish_avg_mark' => $this->finish_avg_mark,
        ]);

        return $dataProvider;
    }
}
