<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\LessonItems;

/**
 * LessonItemsSearch represents the model behind the search form about `common\models\education\LessonItems`.
 */
class LessonItemsSearch extends LessonItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'lesson_test_id', 'lesson_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['lesson_topic', 'lesson_rem'], 'safe'],
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
        $query = LessonItems::find();

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
            'lesson_test_id' => $this->lesson_test_id,
            'lesson_date' => $this->lesson_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'lesson_topic', $this->lesson_topic])
            ->andFilterWhere(['like', 'lesson_rem', $this->lesson_rem]);

        return $dataProvider;
    }
}
