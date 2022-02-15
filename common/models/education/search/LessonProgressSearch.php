<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\LessonProgress;

/**
 * LessonProgressSearch represents the model behind the search form about `common\models\education\LessonProgress`.
 */
class LessonProgressSearch extends LessonProgress
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lesson_items_id', 'studyplan_subject_id', 'lesson_test_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['mark', 'mark_rem'], 'safe'],
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
        $query = LessonProgress::find();

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
            'lesson_items_id' => $this->lesson_items_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'lesson_test_id' => $this->lesson_test_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'mark', $this->mark])
            ->andFilterWhere(['like', 'mark_rem', $this->mark_rem]);

        return $dataProvider;
    }
}
