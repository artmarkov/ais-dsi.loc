<?php

namespace common\models\parents\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\parents\Parents;

/**
 * ParentsSearch represents the model behind the search form about `common\models\parents\Parents`.
 */
class ParentsSearch extends Parents
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'sert_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['sert_name', 'sert_series', 'sert_num', 'sert_organ'], 'safe'],
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
        $query = Parents::find();

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
            'user_common_id' => $this->user_common_id,
            'sert_date' => $this->sert_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'sert_name', $this->sert_name])
            ->andFilterWhere(['like', 'sert_series', $this->sert_series])
            ->andFilterWhere(['like', 'sert_num', $this->sert_num])
            ->andFilterWhere(['like', 'sert_organ', $this->sert_organ]);

        return $dataProvider;
    }
}
