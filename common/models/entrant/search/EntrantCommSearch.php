<?php

namespace common\models\entrant\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\entrant\EntrantComm;

/**
 * EntrantCommSearch represents the model behind the search form about `common\models\entrant\EntrantComm`.
 */
class EntrantCommSearch extends EntrantComm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'division_id', 'plan_year', 'leader_id', 'secretary_id', 'version'], 'integer'],
            [['name', 'members_list', 'prep_on_test_list', 'prep_off_test_list', 'description'], 'safe'],
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
        $query = EntrantComm::find();

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
            'division_id' => $this->division_id,
            'plan_year' => $this->plan_year,
            'leader_id' => $this->leader_id,
            'secretary_id' => $this->secretary_id,
            'timestamp_in' => $this->timestamp_in,
            'timestamp_out' => $this->timestamp_out,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'members_list', $this->members_list])
            ->andFilterWhere(['like', 'prep_on_test_list', $this->prep_on_test_list])
            ->andFilterWhere(['like', 'prep_off_test_list', $this->prep_off_test_list])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
