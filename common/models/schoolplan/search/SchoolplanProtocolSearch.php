<?php

namespace common\models\schoolplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\schoolplan\SchoolplanProtocol;

/**
 * SchoolplanProtocolSearch represents the model behind the search form about `common\models\schoolplan\SchoolplanProtocol`.
 */
class SchoolplanProtocolSearch extends SchoolplanProtocol
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schoolplan_id', 'protocol_date', 'leader_id', 'secretary_id', 'subject_list'], 'integer'],
            [['protocol_name', 'description', 'members_list'], 'safe'],
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
            'protocol_date' => $this->protocol_date,
            'leader_id' => $this->leader_id,
            'secretary_id' => $this->secretary_id,
            'subject_list' => $this->subject_list,

        ]);

        $query->andFilterWhere(['like', 'protocol_name', $this->protocol_name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'members_list', $this->members_list]);

        return $dataProvider;
    }
}
