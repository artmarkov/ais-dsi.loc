<?php

namespace common\models\venue\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\venue\VenueSity;

/**
 * VenueSitySearch represents the model behind the search form about `common\models\venue\VenueSity`.
 */
class VenueSitySearch extends VenueSity
{
    public $countryName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'country_id'], 'integer'],
            [['name'], 'safe'],
            //[['latitude', 'longitude'], 'number'],
            ['countryName', 'string'],
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
        $query = VenueSity::find();

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

        $dataProvider->setSort([
            'attributes' => [
                'id',
//                'latitude',
//                'longitude',
                'name',
                'countryName' => [
                    'asc' => ['venue_country.name' => SORT_ASC],
                    'desc' => ['venue_country.name' => SORT_DESC],
                    'label' => Yii::t('art/guide', 'Name Country')
                ]
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
            $query->joinWith(['country']);

        $query->andWhere(['not', ['venue_sity.id' => 0]]); // убираем запись с 0 ид - 'Не определено'
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        $query->andFilterWhere(['like', 'venue_sity.name', $this->name]);

//        $query->joinWith(['country' => function ($q) {
//            $q->where('venue_sity.latitude LIKE "%' . $this->latitude . '%"');
//        }]);
//
//        $query->joinWith(['country' => function ($q) {
//            $q->where('venue_sity.longitude LIKE "%' . $this->longitude . '%"');
//        }]);

        $query->joinWith(['country' => function ($q) {
            $q->where('venue_country.name LIKE "%' . $this->countryName . '%"');
        }]);
        return $dataProvider;
    }
}
