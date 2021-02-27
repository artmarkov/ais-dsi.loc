<?php

namespace common\models\creative\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\creative\CreativeWorks;

/**
 * CreativeWorksSearch represents the model behind the search form about `common\models\creative\CreativeWorks`.
 */
class CreativeWorksSearch extends CreativeWorks
{
    public $published_at_operand;
    public $categoryName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'comment_status', 'created_by', 'updated_by'], 'integer'],
            [['published_at_operand', 'category_id', 'name', 'description', 'published_at', 'created_at', 'updated_at'], 'safe'],
            ['categoryName', 'string'],
            [['gridDepartmentSearch','gridAuthorSearch'], 'string'],
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
        $query = CreativeWorks::find();

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
//        жадная загрузка
        $query->joinWith(['category']);
        
        $query->with(['creativeWorksDepartments']);
        $query->with(['departmentItem']);
        
        $query->with(['creativeWorksAuthors']);
        $query->with(['authorItem']);
        
        if ($this->gridDepartmentSearch) {
            $query->joinWith(['creativeWorksDepartments']);
        }
        
        if ($this->gridAuthorSearch) {
            $query->joinWith(['creativeWorksAuthors']);
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'comment_status' => $this->comment_status,
            'creative_works_department.department_id' => $this->gridDepartmentSearch,
            'creative_works_author.author_id' => $this->gridAuthorSearch,
            ]);

        $query->andFilterWhere([($this->published_at_operand) ? $this->published_at_operand : '=', 'published_at', ($this->published_at) ? strtotime($this->published_at) : null]);

        $query->andFilterWhere(['like', 'category_id', $this->category_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
