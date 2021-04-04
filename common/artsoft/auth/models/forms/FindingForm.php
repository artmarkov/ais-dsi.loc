<?php
/**
 * Created by PhpStorm.
 * User: Артур
 * Date: 17.06.2018
 * Time: 19:58
 */

namespace artsoft\auth\models\forms;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use artsoft\auth\AuthModule;
use common\models\user\UserCommon;
use Yii;
use yii\base\Model;
use dosamigos\transliterator\TransliteratorHelper;

class FindingForm extends UserCommon
{

    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;
    public $captcha;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'birth_date'], 'required'],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['birth_date'],'safe'],
            ['captcha', 'captcha', 'captchaAction' => '/auth/default/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'birth_date' => Yii::t('art', 'Birth Date'),
            'captcha' => Yii::t('art', 'Captcha'),
        ];
    }

    /**
     * Finds user by fio and birth-date
     * @param $model
     * @return array|UserCommon|null|\yii\db\ActiveRecord
     */
    public static function findByFio($model)
    {
        $birth_date = Yii::$app->formatter->asTimestamp($model->birth_date);

        $user = UserCommon::find()->joinWith('user')
            ->where(['=', 'users.status', User::STATUS_INACTIVE])
            ->andWhere(['=', 'user_common.status', UserCommon::STATUS_ACTIVE])
            ->andWhere(['like', 'last_name', $model->last_name])
            ->andWhere(['like', 'first_name', $model->first_name]);

        if ($model->middle_name != null) {
            $user = $user->andWhere(['like', 'middle_name', $model->middle_name]);
        }
        $user = $user->andWhere(['=', 'birth_date', $birth_date]);

        return $user->one();
    }
    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    /*public static function findInactiveByUsername($username)
    {
        return User::findOne(['username' => $username, 'status' => User::STATUS_INACTIVE]);
    }*/

    /**
     * Slug translit
     *
     * @param  string $slug
     * @return static|null
     */
    protected static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = preg_replace('/[^\p{L}\p{Nd}]+/u', $replacement, $string);
        $string = TransliteratorHelper::process($string, 'UTF-8');
        return $lowercase ? mb_strtolower($string) : $string;
    }
}