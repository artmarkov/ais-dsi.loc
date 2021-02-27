<?php

namespace common\models\teachers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\BonusItem;

/**
 * BonusItemSearch represents the model behind the search form about `common\models\teachers\BonusItem`.
 */
class BonusItemSearch extends BonusItem
{
    public $bonusCategoryName;
    public $measureValueSlugName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bonus_category_id'], 'integer'],
            [['name', 'slug', 'value_default', 'bonus_rule_id', 'status'], 'safe'],
            [['bonusCategoryName','measureValueSlugName'], 'string'],
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
        $query = BonusItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'bonus_category_id',
                'name',
                'slug',
                'measureValueSlugName' =>
                    [
                        'asc' => ['value_default' => SORT_ASC, 'measure_id' => SORT_ASC],
                        'desc' => ['value_default' => SORT_DESC, 'measure_id' => SORT_DESC],
                        'label' => Yii::t('art/teachers', 'Value Default')

                    ],
                'bonus_rule_id',
                'status',

            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
        $query->joinWith(['bonusCategory']);

        $query->andFilterWhere([
            'id' => $this->id,
            'bonus_category_id' => $this->bonus_category_id,
            'status' => $this->status,

        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'bonus_rule_id', $this->bonus_rule_id])
        ;

        $query->andWhere('value_default LIKE "%' . $this->measureValueSlugName . '%" ');

        return $dataProvider;
    }
}
