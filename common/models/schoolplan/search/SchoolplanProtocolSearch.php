<?php

namespace common\models\schoolplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\schoolplan\SchoolplanProtocol;

/**
 * SchoolplanProtocolItemsSearch represents the model behind the search form about `common\models\schoolplan\SchoolplanProtocol`.
 */
class SchoolplanProtocolSearch extends SchoolplanProtocol
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schoolplan_id', 'studyplan_subject_id', 'lesson_mark_id'], 'integer'],
            [['thematic_items_list', 'resume'], 'safe'],
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
        $query = SchoolplanProtocol::find();

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
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'lesson_mark_id' => $this->lesson_mark_id,
        ]);

        $query->andFilterWhere(['like', 'thematic_items_list', $this->thematic_items_list])
            ->andFilterWhere(['like', 'resume', $this->resume]);

        return $dataProvider;
    }
}
