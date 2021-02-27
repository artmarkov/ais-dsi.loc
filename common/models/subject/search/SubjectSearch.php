<?php

namespace common\models\subject\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\subject\Subject;

/**
 * SubjectSearch represents the model behind the search form about `common\models\subject\Subject`.
 */
class SubjectSearch extends Subject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order'], 'integer'],
            [['name', 'slug', 'status'], 'safe'],
            [['gridCategorySearch', 'gridDepartmentSearch'], 'string'],
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
        $query = Subject::find();
        
        // очень жадная загрузка ))
        $query->with(['subjectCategories']);
        $query->with(['subjectCategoryItem']);
        $query->with(['subjectDepartments']);
        $query->with(['departmentItem']);

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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->gridCategorySearch) {
            $query->joinWith(['subjectCategories']);
        }
        if ($this->gridDepartmentSearch) {
            $query->joinWith(['subjectDepartments']);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'subject_category.category_id' => $this->gridCategorySearch,
            'subject_department.department_id' => $this->gridDepartmentSearch,
            'order' => $this->order,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
