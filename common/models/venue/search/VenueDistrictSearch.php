<?php

namespace common\models\venue\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\venue\VenueDistrict;

/**
 * VenueDistrictSearch represents the model behind the search form about `common\models\venue\VenueDistrict`.
 */
class VenueDistrictSearch extends VenueDistrict
{
    public $sityName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['name', 'slug'], 'safe'],
            ['sityName', 'string'],
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
        $query = VenueDistrict::find();

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
                'name',
                'slug',
                'sityName' => [
                    'asc' => ['guide_venue_sity.name' => SORT_ASC],
                    'desc' => ['guide_venue_sity.name' => SORT_DESC],
                    'label' => Yii::t('art/guide', 'Name Sity')
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
            $query->joinWith(['sity']);

        $query->andWhere(['not', ['guide_venue_district.id' => 0]]); // убираем запись с 0 ид - 'Не определено'
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'guide_venue_district.name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        $query->joinWith(['sity' => function ($q) {
            $q->andFilterWhere(['like','guide_venue_sity.name', $this->sityName]);
        }]);

        return $dataProvider;
    }
}
