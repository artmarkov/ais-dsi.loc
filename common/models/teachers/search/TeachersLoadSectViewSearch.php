<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadSectView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersLoadSectViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadSectView`.
 */
class TeachersLoadSectViewSearch extends TeachersLoadSectView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject_sect_id', 'plan_year', 'subject_sect_studyplan_id', 'teachers_load_id', 'direction_id', 'teachers_id'], 'integer'],
            [['studyplan_subject_list'], 'string'],
            [['teachers_load_week_time'], 'number'],
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
        $query = TeachersLoadSectView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => false
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'subject_sect_id' => $this->subject_sect_id,
            'plan_year' => $this->plan_year,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'teachers_load_week_time' => $this->teachers_load_week_time,
        ]);
        $query->andFilterWhere(['like', 'studyplan_subject_list', $this->studyplan_subject_list]);
        return $dataProvider;
    }
}
