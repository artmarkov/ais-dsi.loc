<?php

namespace common\models\entrant\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\entrant\EntrantGroup;

/**
 * EntrantGroupSearch represents the model behind the search form about `common\models\entrant\EntrantGroup`.
 */
class EntrantGroupSearch extends EntrantGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'comm_id', 'prep_flag', 'timestamp_in', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['name', 'description'], 'safe'],
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
        $query = EntrantGroup::find();

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
            'comm_id' => $this->comm_id,
            'prep_flag' => $this->prep_flag,
            'timestamp_in' => $this->timestamp_in,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
