<?php

namespace common\models\service\search;

use common\models\user\UserCommon;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\service\ServiceCardView;

/**
 * ServiceCardViewSearch represents the model behind the search form about `common\models\service\ServiceCardView`.
 */
class ServiceCardViewSearch extends ServiceCardView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_common_id', 'users_card_id', 'status'], 'integer'],
            [['user_category', 'user_category_name', 'user_name', 'phone', 'phone_optional', 'email'], 'string'],
            [['key_hex', 'timestamp_deny', 'mode_main', 'mode_list'], 'safe'],
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
        $query = ServiceCardView::find()->andWhere(['status' => UserCommon::STATUS_ACTIVE]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => false,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_common_id' => $this->user_common_id,
            'users_card_id' => $this->users_card_id,
            'timestamp_deny' => $this->timestamp_deny,
        ]);

        $query->andFilterWhere(['like', 'user_category', $this->user_category])
            ->andFilterWhere(['like', 'user_category_name', $this->user_category_name])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_optional', $this->phone_optional])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'key_hex', $this->key_hex])
            ->andFilterWhere(['like', 'mode_main', $this->mode_main])
            ->andFilterWhere(['like', 'mode_list', $this->mode_list]);

        return $dataProvider;
    }
}
