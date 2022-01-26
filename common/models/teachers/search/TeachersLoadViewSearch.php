<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersLoadViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadView`.
 */
class TeachersLoadViewSearch extends TeachersLoadView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'studyplan_id', 'programm_id','speciality_id','course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'status'], 'integer'],
            [['teachers_load_week_time', 'week_time', 'year_time'], 'number'],
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
        $query = TeachersLoadView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'teachers_id' => SORT_DESC,
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
            'teachers_load_id' => $this->teachers_load_id,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'teachers_load_week_time' => $this->teachers_load_week_time,
            'studyplan_id' => $this->studyplan_id,
            'programm_id' => $this->programm_id,
            'speciality_id' => $this->speciality_id,
            'course' => $this->course,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'week_time' => $this->week_time,
            'year_time' => $this->year_time,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
