<?php

namespace common\models\own\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\own\Department;

/**
 * DepartmentSearch represents the model behind the search form about `common\models\own\Department`.
 */
class DepartmentSearch extends Department
{
    public $divisionName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'division_id', 'name', 'slug', 'status'], 'safe'],
            ['divisionName', 'string'],
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
        $query = Department::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'division_id',
                'name',
                'slug',
                'status',
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
        $query->joinWith(['division']);

        $query->andFilterWhere([
            'guide_department.id' => $this->id,
            'division_id' => $this->division_id,
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'guide_department.slug', $this->slug])
              ->andFilterWhere(['like', 'guide_department.name', $this->name]);

        return $dataProvider;
    }
}
