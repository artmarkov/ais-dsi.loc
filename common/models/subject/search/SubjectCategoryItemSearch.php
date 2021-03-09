<?php

namespace common\models\subject\search;

use common\models\subject\SubjectCategoryItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubjectCategoryItemSearch represents the model behind the search form about `common\models\subject\SubjectCategoryItem`.
 */
class SubjectCategoryItemSearch extends SubjectCategoryItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sortOrder'], 'integer'],
            [['name', 'slug', 'status'], 'safe'],
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
        $query = SubjectCategoryItem::find();
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'sortOrder' => SORT_ASC,
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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
