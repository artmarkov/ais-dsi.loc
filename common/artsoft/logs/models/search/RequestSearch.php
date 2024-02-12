<?php

namespace artsoft\logs\models\search;

use artsoft\models\Request;

use artsoft\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RequestSearch extends Request
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['http_status'], 'integer'],
            [['user_id', 'created_at'], 'safe'],
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
        $query = Request::find();

        $query->joinWith(['user']);

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
            return $dataProvider;
        }
        if ($this->created_at) {
            $tmp = explode(' - ', $this->created_at);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.created_at',
                    $tmp[0], $tmp[1]]);
            }
        }
        $query->andFilterWhere([
            'http_status' => $this->http_status,

        ]);
        $query->andFilterWhere(['like', User::tableName() . '.username', $this->user_id]);
        return $dataProvider;
    }

}
