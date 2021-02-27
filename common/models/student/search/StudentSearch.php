<?php

namespace common\models\student\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\student\Student;

/**
 * StudentSearch represents the model behind the search form about `common\models\student\Student`.
 */
class StudentSearch extends Student
{
    public $studentsFullName;
    public $birth_timestamp;
    public $birth_timestamp_operand;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['position_id','birth_timestamp_operand'], 'safe'],
            [['studentsFullName','birth_timestamp'], 'string'],
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
        $query = Student::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'studentsFullName' => SORT_ASC,
                ],
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'position_id',
                'birth_timestamp',
                'studentsFullName' => [
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
            'user_id' => $this->user_id,

        ]);

        $query->andFilterWhere([($this->birth_timestamp_operand) ? $this->birth_timestamp_operand : '=', 'birth_timestamp', ($this->birth_timestamp) ? strtotime($this->birth_timestamp) : null]);    
        
        $query->andFilterWhere(['like', 'position_id', $this->position_id]);

        $query->andWhere('first_name LIKE "%' . $this->studentsFullName . '%" '.
            'OR last_name LIKE "%' . $this->studentsFullName . '%"'.
            'OR middle_name LIKE "%' . $this->studentsFullName . '%"'
        );
       
        return $dataProvider;
    }
}
