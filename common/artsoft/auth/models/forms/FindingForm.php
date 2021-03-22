<?php
/**
 * Created by PhpStorm.
 * User: Артур
 * Date: 17.06.2018
 * Time: 19:58
 */

namespace artsoft\auth\models\forms;

use artsoft\models\User;
use artsoft\auth\AuthModule;
use Yii;
use yii\base\Model;
use dosamigos\transliterator\TransliteratorHelper;

class FindingForm extends User
{

    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;
    public $birth_timestamp;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            ['birth_date','required'],
//            ['birth_date','validateDateCorrect'],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['birth_timestamp'],'integer'],
//            ['captcha', 'captcha', 'captchaAction' => '/auth/default/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'birth_date' => Yii::t('art', 'Birth Date'),
            'captcha' => Yii::t('art', 'captcha'),
        ];
    }

    /**
     * Finds user by fio and birth-date
     *
     * @param  string $last_name, $first_name, $middle_name, $birth_timestamp, $status
     * @return static|null|User
     */

    public static function findByFio($last_name, $first_name, $middle_name, $birth_timestamp, $status)
    {
        return User::findOne([
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'birth_timestamp' => $birth_timestamp,
            'status' => $status,
        ]);
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

    public static function generateUsername($last_name, $first_name, $middle_name)
    {
        $last_name = static::slug($last_name);
        $first_name = static::slug($first_name);
        $middle_name = static::slug($middle_name);

        $i = 0;

        do {
            $username = $last_name . '-' . substr($first_name, 0, ++$i) . substr($middle_name, 0, 1);
        } while (User::findByUsername($username));

        return $username;
    }
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