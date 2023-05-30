<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\EntrantPreregistrations;

/**
 * EntrantPreregistrationsSearch represents the model behind the search form about `common\models\education\EntrantPreregistrations`.
 */
class EntrantPreregistrationsSearch extends EntrantPreregistrations
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'entrant_programm_id', 'plan_year', 'student_id', 'reg_vid', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
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
        $query = EntrantPreregistrations::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'entrant_programm_id' => SORT_ASC,
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
            'entrant_programm_id' => $this->entrant_programm_id,
            'plan_year' => $this->plan_year,
            'student_id' => $this->student_id,
            'reg_vid' => $this->reg_vid,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
