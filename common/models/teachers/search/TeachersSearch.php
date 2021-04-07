<?php

namespace common\models\teachers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\Teachers;

/**
 * TeachersSearch represents the model behind the search form about `common\models\teachers\Teachers`.
 */
class TeachersSearch extends Teachers
{
    public $fullName;
    public $userStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'year_serv', 'year_serv_spec', 'date_serv', 'date_serv_spec', 'userStatus'], 'integer'],
            [['position_id', 'level_id', 'tab_num', 'bonus_summ'], 'safe'],
            ['fullName', 'string'],
            [['department_list', 'bonus_list'], 'string'],
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
        $query = Teachers::find();

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
                'position_id',
                'work_id',
                'level_id',
                'tab_num',
                'bonus_summ',
                'userStatus',
//                'bonus_list',
//                'department_list',
                'year_serv',
                'year_serv_spec',
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
            'teachers.id' => $this->id,
            'position_id' => $this->position_id,
            'level_id' => $this->level_id,
            'year_serv' => $this->year_serv,
            'year_serv_spec' => $this->year_serv_spec,
            'bonus_summ' => $this->bonus_summ,
            'user_common.status' => $this->userStatus,
        ]);

        $query->andFilterWhere(['like', 'department_list', $this->department_list]);
        $query->andFilterWhere(['like', 'bonus_list', $this->bonus_list]);
        $query->andFilterWhere(['like', 'tab_num', $this->tab_num]);

        if ($this->fullName) {
            $query->andFilterWhere(['like', 'first_name', $this->fullName])
                ->orFilterWhere(['like', 'last_name', $this->fullName])
                ->orFilterWhere(['like', 'middle_name', $this->fullName]);

        }
        return $dataProvider;
    }
}
