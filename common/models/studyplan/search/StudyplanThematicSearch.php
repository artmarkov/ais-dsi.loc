<?php

namespace common\models\studyplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\studyplan\StudyplanThematic;

/**
 * StudyplanThematicSearch represents the model behind the search form about `common\models\studyplan\StudyplanThematic`.
 */
class StudyplanThematicSearch extends StudyplanThematic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'period_in', 'period_out', 'template_flag', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['template_name'], 'safe'],
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
        $query = StudyplanThematic::find();

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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'thematic_category' => $this->thematic_category,
            'period_in' => $this->period_in,
            'period_out' => $this->period_out,
            'template_flag' => $this->template_flag,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'template_name', $this->template_name]);

        return $dataProvider;
    }
}
