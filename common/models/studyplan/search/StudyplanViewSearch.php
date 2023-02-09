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
            [['id', 'student_id', 'course', 'plan_year', 'status', 'programm_id'], 'integer'],
            [['education_programm_name', 'education_programm_short_name', 'education_cat_name', 'education_cat_short_name', 'student_fio'], 'safe'],
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
            'pagination' => false,
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
            'id' => $this->id,
            'programm_id' => $this->programm_id,
            'student_id' => $this->student_id,
            'course' => $this->course,
            'plan_year' => $this->plan_year,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
            $query->andFilterWhere(['like', 'education_programm_name', $this->education_programm_name]);
            $query->andFilterWhere(['like', 'education_programm_short_name', $this->education_programm_short_name]);
            $query->andFilterWhere(['like', 'education_cat_name', $this->education_cat_name]);
            $query->andFilterWhere(['like', 'education_cat_short_name', $this->education_cat_short_name]);
            $query->andFilterWhere(['like', 'student_fio', $this->student_fio]);


        return $dataProvider;
    }
}
