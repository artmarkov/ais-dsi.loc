<?php

namespace common\models\efficiency\search;

use common\models\schedule\ConsultScheduleView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\efficiency\TeachersEfficiency;

/**
 * TeachersEfficiencySearch represents the model behind the search form about `common\models\efficiency\TeachersEfficiency`.
 */
class TeachersEfficiencySearch extends TeachersEfficiency
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: ConsultScheduleView::find();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'efficiency_id', 'teachers_id', 'bonus_vid_id', 'version', 'item_id'], 'integer'],
            [['bonus', 'date_in', 'class'], 'safe'],
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
        if ($this->date_in) {
            $tmp = explode(' - ', $this->date_in);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.date_in',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'item_id' => $this->item_id,
            'bonus_vid_id' => $this->bonus_vid_id,
            'efficiency_id' => $this->efficiency_id,
            'teachers_id' => $this->teachers_id,
//            'date_in' => $this->date_in,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'bonus', $this->bonus]);
        $query->andFilterWhere(['like', 'class', $this->class]);

        return $dataProvider;
    }
}
