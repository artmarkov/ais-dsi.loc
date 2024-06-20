<?php

namespace common\models\studyplan\search;

use common\models\studyplan\StudyplanView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StudyplanSearch represents the model behind the search form about `common\models\studyplan\StudyplanView`.
 */
class StudyplanViewSearch extends StudyplanView
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'course', 'plan_year', 'status', 'status_reason', 'subject_form_id', 'student_id'], 'integer'],
            [['education_programm_name', 'education_programm_short_name', 'education_cat_name', 'education_cat_short_name', 'student_fio', 'subject_type_name'], 'safe'],
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
        $query = StudyplanView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'student_fio' => SORT_ASC,
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
            'course' => $this->course,
            'plan_year' => $this->plan_year,
            'subject_form_id' => $this->subject_form_id,
            'status' => $this->status,
            'status_reason' => $this->status_reason,
            'student_id' => $this->student_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
            $query->andFilterWhere(['like', 'education_programm_name', $this->education_programm_name]);
            $query->andFilterWhere(['like', 'education_programm_short_name', $this->education_programm_short_name]);
            $query->andFilterWhere(['like', 'education_cat_name', $this->education_cat_name]);
            $query->andFilterWhere(['like', 'education_cat_short_name', $this->education_cat_short_name]);
            $query->andFilterWhere(['like', 'student_fio', $this->student_fio]);
            $query->andFilterWhere(['like', 'subject_form_name', $this->subject_form_name]);


        return $dataProvider;
    }
}
