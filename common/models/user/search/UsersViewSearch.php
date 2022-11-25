<?php

namespace common\models\user\search;

use common\models\user\UsersView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UsersViewSearch represents the model behind the search form about `common\models\user\UsersView`.
 */
class UsersViewSearch extends UsersView
{

    public function rules()
    {
        return [
            [['id', 'superadmin', 'status', 'created_at', 'updated_at', 'email_confirmed', 'user_common_id'], 'integer'],
            [['username', 'gridRoleSearch', 'registration_ip', 'email'], 'string'],
            [['user_category_name', 'user_name', 'phone', 'phone_optional', 'user_common_status'], 'string'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UsersView::find();

        $query->with(['roles']);

        if (!Yii::$app->user->isSuperadmin) {
            $query->where(['superadmin' => 0]);
        }

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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->gridRoleSearch) {
            $query->joinWith(['roles']);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'superadmin' => $this->superadmin,
            'status' => $this->status,
            Yii::$app->art->auth_item_table . '.name' => $this->gridRoleSearch,
            'registration_ip' => $this->registration_ip,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email_confirmed' => $this->email_confirmed,
            'user_common_id' => $this->user_common_id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'user_category_name', $this->user_category_name])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_optional', $this->phone_optional])
            ->andFilterWhere(['like', 'user_common_status', $this->user_common_status])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}