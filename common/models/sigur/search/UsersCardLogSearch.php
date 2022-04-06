<?php

namespace common\models\sigur\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\sigur\UsersCardLog;

/**
 * UsersCardLogSearch represents the model behind the search form about `common\models\sigur\UsersCardLog`.
 */
class UsersCardLogSearch extends UsersCardLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dir_code', 'evtype_code'], 'integer'],
            [['key_hex', 'datetime', 'deny_reason', 'dir_name', 'evtype_name', 'name', 'position'], 'safe'],
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
        $query = UsersCardLog::find();

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

        if ($this->datetime) {
            $tmp = explode(' - ', $this->datetime);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.datetime',
                    Yii::$app->formatter->asDate($tmp[0], 'php:Y-m-d H:i:s'),
                    Yii::$app->formatter->asDate($tmp[1], 'php:Y-m-d H:i:s')
                ]);
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'dir_code' => $this->dir_code,
            'evtype_code' => $this->evtype_code,
        ]);

        $query
            ->andFilterWhere(['like', 'key_hex', $this->key_hex])
            ->andFilterWhere(['like', 'deny_reason', $this->deny_reason])
            ->andFilterWhere(['like', 'dir_name', $this->dir_name])
            ->andFilterWhere(['like', 'evtype_name', $this->evtype_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'position', $this->position]);

        return $dataProvider;
    }
}
