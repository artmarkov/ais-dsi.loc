<?php

namespace common\models\venue\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\venue\VenuePlace;

/**
 * VenuePlaceSearch represents the model behind the search form about `common\models\venue\VenuePlace`.
 */
class VenuePlaceSearch extends VenuePlace
{
    public $countryName;
    public $districtName;
    public $districtSlug;
    public $sityName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'country_id'],'integer'],
            [['name', 'address', 'phone'], 'safe'],
           // [['latitude', 'longitude'], 'number'],
            [['countryName', 'districtSlug', 'sityName'], 'string'],
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
        $query = VenuePlace::find();

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
                'address',
                'country_id',
//                'countryName' => [
//                    'asc' => ['venue_country.name' => SORT_ASC],
//                    'desc' => ['venue_country.name' => SORT_DESC]
//                ],
                'districtSlug' => [
                    'asc' => ['venue_district.slug' => SORT_ASC],
                    'desc' => ['venue_district.slug' => SORT_DESC]
                ],
                'districtName' => [
                    'asc' => ['venue_district.name' => SORT_ASC],
                    'desc' => ['venue_district.name' => SORT_DESC]
                ],
                'sityName' => [
                    'asc' => ['venue_sity.name' => SORT_ASC],
                    'desc' => ['venue_sity.name' => SORT_DESC]
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
            $query->joinWith(['district']);
            $query->joinWith(['sity']);

        $query->andFilterWhere([
            'id' => $this->id, 
            'venue_place.country_id' => $this->country_id
            
        ]);


        $query->andFilterWhere(['like', 'venue_place.name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address]);

//        $query->joinWith(['country' => function ($q) {
//            $q->where('venue_place.latitude LIKE "%' . $this->latitude . '%"');
//        }]);
//
//        $query->joinWith(['country' => function ($q) {
//            $q->where('venue_place.longitude LIKE "%' . $this->longitude . '%"');
//        }]);

//        $query->joinWith(['country' => function ($q) {
//            $q->where('venue_country.name LIKE "%' . $this->countryName . '%"'); 
//        }]);

//        $query->joinWith(['district' => function ($q) {
//           $q->where('venue_district.name LIKE "%' . $this->districtName . '%"');
//        }]);
        $query->joinWith(['district' => function ($q) {
            $q->where('venue_district.slug LIKE "%' . $this->districtSlug . '%"');
        }]);
        $query->joinWith(['sity' => function ($q) {
            $q->where('venue_sity.name LIKE "%' . $this->sityName . '%"');
        }]);
        
        return $dataProvider;
    }
}
