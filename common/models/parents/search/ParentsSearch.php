<?php

namespace common\models\parents\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\parents\Parents;

/**
 * ParentsSearch represents the model behind the search form about `common\models\parents\Parents`.
 */
class ParentsSearch extends Parents
{
    public $fullName;
    public $userStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'sert_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'userStatus'], 'integer'],
            [['sert_name', 'sert_series', 'sert_num', 'sert_organ'], 'safe'],
            ['fullName', 'string'],
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
        $query = Parents::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'userStatus' => SORT_DESC,
                'fullName' => SORT_ASC,
            ],
            'attributes' => [
                'id',
                'sert_date',
                'sert_name',
                'sert_series',
                'sert_num',
                'sert_organ',
                'userStatus' => [
                    'asc' => ['user_common.status' => SORT_ASC],
                    'desc' => ['user_common.status' => SORT_DESC],
                ],
                'fullName' => [
                    'asc' => ['last_name' => SORT_ASC, 'first_name' => SORT_ASC, 'middle_name' => SORT_ASC],
                    'desc' => ['last_name' => SORT_DESC, 'first_name' => SORT_DESC, 'middle_name' => SORT_DESC],
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
        $query->joinWith(['user']);

        $query->andFilterWhere([
            'id' => $this->id,
            'user_common_id' => $this->user_common_id,
            'sert_date' => $this->sert_date,
            'user_common.status' => $this->userStatus,
        ]);

        $query->andFilterWhere(['like', 'sert_name', $this->sert_name])
            ->andFilterWhere(['like', 'sert_series', $this->sert_series])
            ->andFilterWhere(['like', 'sert_num', $this->sert_num])
            ->andFilterWhere(['like', 'sert_organ', $this->sert_organ]);

        if ($this->fullName) {
            $query->andFilterWhere(['like', 'first_name', $this->fullName])
                ->orFilterWhere(['like', 'last_name', $this->fullName])
                ->orFilterWhere(['like', 'middle_name', $this->fullName]);

        }
        return $dataProvider;
    }
}
