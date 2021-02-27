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
    public $teachersFullName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'timestamp_serv', 'timestamp_serv_spec'], 'integer'],
            [['position_id', 'work_id', 'level_id', 'tab_num'], 'safe'],
            ['teachersFullName', 'string'],
            ['gridDepartmentSearch', 'string'],
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
                'teachersFullName' => [
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
        
        $query->with(['teachersDepartments']);
        $query->with(['departmentItem']);
        
        
        if ($this->gridDepartmentSearch) {
            $query->joinWith(['teachersDepartments']);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'timestamp_serv' => $this->timestamp_serv,
            'timestamp_serv_spec' => $this->timestamp_serv_spec,
            'teachers_department.department_id' => $this->gridDepartmentSearch,
        ]);

        $query->andFilterWhere(['like', 'position_id', $this->position_id])
            ->andFilterWhere(['like', 'work_id', $this->work_id])
            ->andFilterWhere(['like', 'level_id', $this->level_id])
            ->andFilterWhere(['like', 'tab_num', $this->tab_num]);
        
        $query->andWhere('first_name LIKE "%' . $this->teachersFullName . '%" '.
               'OR last_name LIKE "%' . $this->teachersFullName . '%"'. 
               'OR middle_name LIKE "%' . $this->teachersFullName . '%"'
           );
        return $dataProvider;
    }
}
