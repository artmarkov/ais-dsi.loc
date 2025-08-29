<?php

namespace common\models\teachers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\TeachersQualifications;

/**
 * TeachersQualificationsSearch represents the model behind the search form about `common\models\teachers\TeachersQualifications`.
 */
class TeachersQualificationsSearch extends TeachersQualifications
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'teachers_id', 'version', 'status'], 'integer'],
            [['name', 'place'], 'string'],
            [['date'], 'safe'],
            [['description'], 'string'],
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
        $query = TeachersQualifications::find();

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
        if ($this->date) {
            $tmp = explode(' - ', $this->date);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.date',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'teachers_id' => $this->teachers_id,
//            'date' => $this->date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'place', $this->place]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
