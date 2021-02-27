<?php

namespace common\models\service;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\service\Book;

/**
 * BookSearch represents the model behind the search form about `common\models\service\Book`.
 */
class BookSearch extends Book
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'buy_amount'], 'integer'],
            [['name','color'], 'safe'],
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
        $query = Book::find();

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
            'buy_amount' => $this->buy_amount,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
         $query->andFilterWhere(['like', 'color', $this->color]);

        return $dataProvider;
    }
}
