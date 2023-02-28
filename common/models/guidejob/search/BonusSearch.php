<?php

namespace common\models\guidejob\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\guidejob\Bonus;

/**
 * BonusSearch represents the model behind the search form about `common\models\guidejob\Bonus`.
 */
class BonusSearch extends Bonus
{
    public $bonusCategoryName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bonus_category_id', 'bonus_vid_id'], 'integer'],
            [['name', 'slug', 'value_default', 'status'], 'safe'],
            [['bonusCategoryName'], 'string'],
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
        $query = Bonus::find();

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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
        $query->joinWith(['bonusCategory']);

        $query->andFilterWhere([
            'guide_teachers_bonus.id' => $this->id,
            'bonus_vid_id' => $this->bonus_vid_id,
            'bonus_category_id' => $this->bonus_category_id,
            'status' => $this->status,

        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
        ;


        return $dataProvider;
    }
}
