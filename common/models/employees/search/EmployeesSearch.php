<?php

namespace common\models\employees\search;

use artsoft\traits\ParamsTrimable;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\employees\Employees;

/**
 * EmployeesSearch represents the model behind the search form about `common\models\employees\Employees`.
 */
class EmployeesSearch extends Employees
{
    use ParamsTrimable;

    public $fullName;
    public $userStatus;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'userStatus', 'access_work_flag'], 'integer'],
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
        ]);
        $dataProvider->setSort([
            'defaultOrder' => [
                'userStatus' => SORT_DESC,
                'fullName' => SORT_ASC,
            ],
            'attributes' => [
                'id',
                'position',
                'access_work_flag',
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
        $params = $this->trimParams($params, static::class);
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
            'access_work_flag' => $this->access_work_flag,
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
