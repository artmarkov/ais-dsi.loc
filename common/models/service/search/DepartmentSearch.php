<?php

namespace common\models\service\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\service\Department;

/**
 * DepartmentSearch represents the model behind the search form about `common\models\service\Department`.
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

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'division_id', $this->division_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
