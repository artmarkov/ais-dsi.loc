<?php

namespace common\models\studyplan\search;

use common\models\studyplan\ThematicView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StudyplanThematicViewSearch represents the model behind the search form about `common\models\studyplan\ThematicView`.
 */
class ThematicViewSearch extends ThematicView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'plan_year', 'teachers_load_id', 'subject_type_id',  'studyplan_thematic_id', 'thematic_category', 'direction_id', 'teachers_id', 'status', 'doc_sign_teachers_id'], 'integer'],

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
        $query = ThematicView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
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
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'subject_sect_id' => $this->subject_sect_id,
            'plan_year' => $this->plan_year,
//            'course' => $this->course,
            'status' => $this->status,
//            'subject_cat_id' => $this->subject_cat_id,
//            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'teachers_id' => $this->teachers_id,
            'direction_id' => $this->direction_id,
            'doc_sign_teachers_id' => $this->doc_sign_teachers_id,
            'studyplan_thematic_id' => $this->studyplan_thematic_id,
            'thematic_category' => $this->thematic_category,
        ]);


        return $dataProvider;
    }
}
