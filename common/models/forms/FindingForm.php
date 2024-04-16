<?php

namespace common\models\forms;

use Yii;
use yii\base\Model;
use yii\db\Query;

class FindingForm extends Model
{
    public $full_name;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['full_name', 'birth_date'], 'required'],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['full_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['birth_date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('art', 'Full Name'),
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'birth_date' => Yii::t('art', 'Birth Date'),
        ];
    }

    /**
     * Finds user by fio and birth-date
     * @param $model
     * @return array|bool
     */
    public static function findByFio($model)
    {
        $birth_date = Yii::$app->formatter->asTimestamp($model->birth_date);

        $user = (new Query())->from('students_view')
            ->select(['students_id'])
            ->where(['AND',
                ['first_name' => $model->first_name],
                ['last_name' => $model->last_name],
            ]);

        if ($model->middle_name != null) {
            $user = $user->andWhere(['like', 'middle_name', $model->middle_name]);
        }
        $user = $user->andWhere(['=', 'birth_date', $birth_date]);

        return $user->scalar();
    }

    public function afterValidate()
    {
        if ($this->full_name) {
            $fullName = explode(' ', $this->full_name);
            $this->last_name = $fullName[0] ?? '';
            $this->first_name = $fullName[1] ?? '';
            $this->middle_name = $fullName[2] ?? '';
        }
        parent::afterValidate();
    }
}