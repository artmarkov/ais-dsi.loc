<?php

namespace common\models\studyplan\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\studyplan\Studyplan;

/**
 * StudyplanSearch represents the model behind the search form about `common\models\studyplan\Studyplan`.
 */
class StudyplanSearch extends Studyplan
{
    public $programmName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'course', 'plan_year', 'status'], 'integer'],
            [['description', 'programmName'], 'safe'],
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
        $query = Studyplan::find();
        $query->joinWith(['programm','student']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);
        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'attributes' => [
                'id',
                'status',
                'plan_year',
                'course',
                'student_id',

                'programmName' => [
                    'asc' => ['education_programm.name' => SORT_ASC],
                    'desc' => ['education_programm.name' => SORT_DESC],
                ],

            ]
        ]);
        $this->load($params);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course' => $this->course,
            'plan_year' => $this->plan_year,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        if ($this->programmName) {
            $query->andFilterWhere(['like', 'education_programm.name', $this->programmName]);

        }

        return $dataProvider;
    }
}
