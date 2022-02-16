<?php

namespace common\models\education\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\education\LessonMark;

/**
 * LessonMarkSearch represents the model behind the search form about `common\models\education\LessonMark`.
 */
class LessonMarkSearch extends LessonMark
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'sort_order', 'mark_category', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['mark_label'], 'safe'],
            [['mark_value'], 'number'],
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
        $query = LessonMark::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
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
            'mark_value' => $this->mark_value,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'mark_category' => $this->mark_category,

        ]);

        $query->andFilterWhere(['like', 'mark_label', $this->mark_label]);

        return $dataProvider;
    }
}
