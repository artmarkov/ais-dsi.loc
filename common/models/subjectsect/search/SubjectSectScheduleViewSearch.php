<?php

namespace common\models\subjectsect\search;

use common\models\subjectsect\SubjectSectScheduleView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubjectSectScheduleSearch represents the model behind the search form about `common\models\subjectsect\SubjectSectSchedule`.
 * @property int $subject_sect_id
 */
class SubjectSectScheduleViewSearch extends SubjectSectScheduleView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'week_num', 'week_day', 'auditory_id'], 'integer'],
            [['description', 'studyplan_subject_list'], 'safe'],
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
        $query = SubjectSectScheduleView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'subject_sect_studyplan_id' => SORT_DESC,
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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
//            'time_in' => $this->time_in,
//            'time_out' => $this->time_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'studyplan_subject_list', $this->studyplan_subject_list]);

        return $dataProvider;
    }
}
