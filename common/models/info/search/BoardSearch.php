<?php

namespace common\models\info\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\info\Board;

/**
 * BoardSearch represents the model behind the search form about `common\models\info\Board`.
 */
class BoardSearch extends Board
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'importance_id', 'delete_date', 'status', 'version', 'created_by'], 'integer'],
            [['title', 'description', 'recipients_list', 'board_date'], 'safe'],
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
        $query = Board::find();

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
            'category_id' => $this->category_id,
            'importance_id' => $this->importance_id,
            'delete_date' => $this->delete_date,
            'status' => $this->status,
            'version' => $this->version,
            'created_by' => $this->created_by,
        ]);
        if ($this->board_date) {
            $tmp = explode(' - ', $this->board_date);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.board_date',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'recipients_list', $this->recipients_list]);

        return $dataProvider;
    }
}
