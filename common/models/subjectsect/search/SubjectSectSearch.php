<?php

namespace common\models\subjectsect\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\subjectsect\SubjectSect;

/**
 * SubjectSectSearch represents the model behind the search form about `common\models\subjectsect\SubjectSect`.
 */
class SubjectSectSearch extends SubjectSect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id'], 'integer'],
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
        $query = SubjectSect::find();

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
            'plan_year' => $this->plan_year,
            'union_id' => $this->union_id,
            'course' => $this->course,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        return $dataProvider;
    }
}
