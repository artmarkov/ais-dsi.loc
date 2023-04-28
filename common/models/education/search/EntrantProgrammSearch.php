<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\EntrantProgramm;

/**
 * EntrantProgrammSearch represents the model behind the search form about `common\models\education\EntrantProgramm`.
 */
class EntrantProgrammSearch extends EntrantProgramm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',  'programm_id', 'subject_type_id', 'course', 'age_in', 'age_out', 'qty_entrant', 'qty_reserve', 'status'], 'integer'],
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
        $query = EntrantProgramm::find();

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
            'programm_id' => $this->programm_id,
            'subject_type_id' => $this->subject_type_id,
            'course' => $this->course,
            'age_in' => $this->age_in,
            'age_out' => $this->age_out,
            'qty_entrant' => $this->qty_entrant,
            'qty_reserve' => $this->qty_reserve,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
