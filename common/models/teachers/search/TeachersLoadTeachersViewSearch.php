<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadTeachersView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersLoadTeachersViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadTeachersView`.
 */
class TeachersLoadTeachersViewSearch extends TeachersLoadTeachersView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year'], 'integer'],
            [['teachers_load_week_time'], 'number'],
            [['studyplan_subject_list'], 'string'],
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
        $query = TeachersLoadTeachersView::find();

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
            'course' => $this->course,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'plan_year' => $this->plan_year,
            'teachers_load_week_time' => $this->teachers_load_week_time,
        ]);
        $query->andFilterWhere(['like', 'studyplan_subject_list', $this->studyplan_subject_list]);
        return $dataProvider;
    }
}
