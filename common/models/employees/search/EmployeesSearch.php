<?php

namespace common\models\employees\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\employees\Employees;

/**
 * EmployeesSearch represents the model behind the search form about `common\models\employees\Employees`.
 */
class EmployeesSearch extends Employees
{
    public $fullName;
    public $userStatus;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'userStatus'], 'integer'],
            [['position'], 'safe'],
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
        $query = Employees::find();

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
                'position',
                'userStatus',
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
            'user_common.status' => $this->userStatus,
        ]);

        $query->andFilterWhere(['like', 'position', $this->position]);
        if ($this->fullName) {
            $query->andFilterWhere(['like', 'first_name', $this->fullName])
                ->orFilterWhere(['like', 'last_name', $this->fullName])
                ->orFilterWhere(['like', 'middle_name', $this->fullName]);

        }
        return $dataProvider;
    }
}
