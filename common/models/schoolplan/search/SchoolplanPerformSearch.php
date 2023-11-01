<?php

namespace common\models\schoolplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\schoolplan\SchoolplanPerform;

/**
 * SchoolplanPerformSearch represents the model behind the search form about `common\models\schoolplan\SchoolplanPerform`.
 */
class SchoolplanPerformSearch extends SchoolplanPerform
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schoolplan_id', 'teachers_id', 'studyplan_subject_id', 'lesson_mark_id', 'status_exe', 'status_sign', 'signer_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['thematic_items_list', 'winner_id', 'resume'], 'safe'],
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
        $query = SchoolplanPerform::find();

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
            'schoolplan_id' => $this->schoolplan_id,
            'teachers_id' => $this->teachers_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'lesson_mark_id' => $this->lesson_mark_id,
            'status_exe' => $this->status_exe,
            'status_sign' => $this->status_sign,
            'signer_id' => $this->signer_id,
        ]);

        $query->andFilterWhere(['like', 'thematic_items_list', $this->thematic_items_list])
            ->andFilterWhere(['like', 'winner_id', $this->winner_id])
            ->andFilterWhere(['like', 'resume', $this->resume]);

        return $dataProvider;
    }
}
