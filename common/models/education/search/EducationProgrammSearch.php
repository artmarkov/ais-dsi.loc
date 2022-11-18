<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\EducationProgramm;

/**
 * EducationProgrammSearch represents the model behind the search form about `common\models\education\EducationProgramm`.
 */
class EducationProgrammSearch extends EducationProgramm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'education_cat_id',  'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['name', 'speciality_list', 'term_mastering', 'description'], 'safe'],
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
        $query = EducationProgramm::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'education_cat_id' => $this->education_cat_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
           // ->andFilterWhere(['like', 'speciality_list', $this->speciality_list])
            ->andFilterWhere(['like', 'term_mastering', $this->term_mastering])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
