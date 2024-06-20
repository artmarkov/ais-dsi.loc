<?php

namespace common\models\students\search;

use artsoft\helpers\StringHelper;
use artsoft\traits\ParamsTrimable;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\students\Student;

/**
 * StudentSearch represents the model behind the search form about `common\models\students\Student`.
 */
class StudentSearch extends Student
{
    use ParamsTrimable;

    public $fullName;
    public $userStatus;
    public $userBirthDate;
    public $userBirthDate_operand;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userStatus'], 'integer'],
            [['limited_status_list'], 'string'],
            [['fullName', 'userBirthDate', 'userBirthDate_operand'], 'string'],
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
//        жадная загрузка
        $query->joinWith(['user']);

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
                'limited_status_list',
                'userBirthDate' => [
                    'asc' => ['birth_date' => SORT_ASC],
                    'desc' => ['birth_date' => SORT_DESC],
                ],
                'userStatus' => [
                    'asc' => ['status' => SORT_ASC],
                    'desc' => ['status' => SORT_DESC],
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



        $query->andFilterWhere([
            'students.id' => $this->id,
            'user_common.status' => $this->userStatus,

        ]);

        $query->andFilterWhere([($this->userBirthDate_operand) ? $this->userBirthDate_operand : '=', 'birth_date', ($this->userBirthDate) ? strtotime($this->userBirthDate) : null]);
        $query->andFilterWhere(['like', 'limited_status_list', $this->limited_status_list]);

        if ($this->fullName) {
            $this->fullName  = StringHelper::ucfirst($this->fullName);
            $query->andFilterWhere(['like', 'first_name', $this->fullName])
                ->orFilterWhere(['like', 'last_name', $this->fullName])
                ->orFilterWhere(['like', 'middle_name', $this->fullName]);

        }
       
        return $dataProvider;
    }
}
