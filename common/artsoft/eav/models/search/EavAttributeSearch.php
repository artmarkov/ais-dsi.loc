<?php

namespace artsoft\eav\models\search;

use artsoft\eav\models\EavAttribute;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EavAttributeSearch represents the model behind the search form about `artsoft\eav\models\EavAttribute`.
 */
class EavAttributeSearch extends EavAttribute
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'required', 'visible'], 'integer'],
            [['name', 'label', 'default_value', 'description', 'icon'], 'safe'],
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
        $query = EavAttribute::find();

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
            'type_id' => $this->type_id,
            'required' => $this->required,
            'visible' => $this->visible,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'icon', $this->label])
            ->andFilterWhere(['like', 'default_value', $this->default_value])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}