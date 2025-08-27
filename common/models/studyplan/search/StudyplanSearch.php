<?php

namespace common\models\studyplan\search;

use artsoft\helpers\StringHelper;
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
    public $studentFio;
    public $educationCatId;

    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: Studyplan::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'course', 'plan_year', 'status', 'status_reason', 'programm_id', 'subject_form_id', 'educationCatId'], 'integer'],
            [['description', 'programmName', 'studentFio'], 'safe'],
            [['cond_flag'], 'boolean'],
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
        $query = $this->query;

        $query->joinWith(['programm','student'])->innerJoin('user_common', 'user_common.id = students.user_common_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);
        $dataProvider->setSort([
            'defaultOrder' => [
                'studentFio' => SORT_ASC,
            ],
            'attributes' => [
                'id',
                'status',
                'status_reason',
                'cond_flag',
                'plan_year',
                'course',
                'student_id',
                'subject_form_id',
                'education_cat_id',
                'programmName' => [
                    'asc' => ['education_programm.name' => SORT_ASC],
                    'desc' => ['education_programm.name' => SORT_DESC],
                ],
                'educationCatId' => [
                    'asc' => ['education_programm.education_cat_id' => SORT_ASC],
                    'desc' => ['education_programm.education_cat_id' => SORT_DESC],
                ],
                'studentFio' => [
                    'asc' => ['user_common.last_name' => SORT_ASC, 'user_common.first_name' => SORT_ASC],
                    'desc' => ['user_common.last_name' => SORT_DESC, 'user_common.first_name' => SORT_DESC],
                ],
            ]
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
            'education_cat_id' => $this->educationCatId,
            'student_id' => $this->student_id,
            'course' => $this->course,
            'plan_year' => $this->plan_year,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'studyplan.status' => $this->status,
            'studyplan.cond_flag' => $this->cond_flag,
            'version' => $this->version,
            'subject_form_id' => $this->subject_form_id,
            'studyplan.status' => $this->status,
            'status_reason' => $this->status_reason,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        if ($this->programmName) {
            $query->andFilterWhere(['=', 'programm_id', $this->programmName]);
        }
        if ($this->studentFio) {
            $this->studentFio  = StringHelper::ucfirst($this->studentFio);
            $query->andFilterWhere(['=', 'student_id', $this->studentFio]);
        }

        return $dataProvider;
    }
}
