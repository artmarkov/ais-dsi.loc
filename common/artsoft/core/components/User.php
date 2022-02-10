<?php

namespace artsoft\components;

use artsoft\helpers\AuthHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class User
 * @package artsoft\components
 */
class User extends \yii\web\User
{
    /**
     * @inheritdoc
     */
    public $identityClass = 'artsoft\models\User';

    /**
     * @inheritdoc
     */
    public $settingsClass = 'artsoft\models\UserSetting';

    /**
     * Settings identity
     *
     * @var mixed
     */
    private $_settings = false;

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ['/auth/login'];

    /**
     * Allows to call Yii::$app->user->isSuperadmin
     *
     * @return bool
     */
    public function getIsSuperadmin()
    {
        return @Yii::$app->user->identity->superadmin == 1;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return @Yii::$app->user->identity->username;
    }

    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        AuthHelper::updatePermissions($identity);

        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * @inheritdoc
     */
    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        $request = Yii::$app->getRequest();
        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            $this->setReturnUrl($request->getUrl());
        }
        if ($this->loginUrl !== null) {
            $loginUrl = (array)$this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect(Yii::$app->urlManager->hostInfo . $this->loginUrl[0]);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }

//    /**
//     * Returns the settings identity object associated with the currently logged-in user.
//     */
//    public function getSettings($autoRenew = true)
//    {
//        if ($this->_settings === false) {
//            if ($autoRenew) {
//                $this->_settings = null;
//                $this->renewSettings();
//            } else {
//                return null;
//            }
//        }
//
//        return $this->_settings;
//    }
//
//    /**
//     * Sets the user's settings identity object.
//     */
//    public function setSettings($identity)
//    {
//        if ($identity instanceof $this->settingsClass) {
//            $this->_settings = $identity;
//        } elseif ($identity === null) {
//            $this->_settings = null;
//        } else {
//            throw new InvalidValueException("The identity object must implement {$this->settingsClass}.");
//        }
//    }
//
//    protected function renewSettings()
//    {
//        $userId = Yii::$app->user->id;
//        if ($userId === null) {
//            $settings = null;
//        } else {
//            $class = $this->settingsClass;
//            $settings = new $class;
//        }
//
//        $this->setSettings($settings);
//
//    }

    public function getSetting($key, $default = NULL)
    {
        if ($setting = $this->settingsClass::findOne(['user_id' => Yii::$app->user->id, 'key' => $key])) {
            return json_decode($setting->value);
        }

        return $default;
    }

    public function setSetting($key, $value)
    {
        try {
            $setting = $this->settingsClass::findOne(['user_id' => Yii::$app->user->id, 'key' => $key]);
            if ($setting) {
                $setting->value = json_encode($value);
            } else {
                $setting = new $this->settingsClass;
                $setting->user_id = Yii::$app->user->id;
                $setting->key = $key;
                $setting->value = json_encode($value);
            }
            return ($setting->save()) ? TRUE : FALSE;

        } catch (Exception $ex) {
            print_r($ex);
            die;
        }

        return FALSE;
    }

    /**
     * Удаление атрибута
     * @param string|array $keys список названий настроек
     */
    public function delSetting($keys)
    {
        $this->settingsClass::deleteAll(['user_id' => Yii::$app->user->id, 'key' => $keys]);
    }

    /**
     * Возвращает массив значений атрибутов
     * @param array $keys список запрашиваемых атрибутов
     * @param array $defaults массив со значениями по умолчанию
     * @return array
     */
    public function getSettingList($keys, $defaults = [])
    {
        $result = [];
        $list = $this->settingsClass::find()->where(['user_id' => Yii::$app->user->id, 'key' => $keys])->all();
        foreach ($list as $m) {
            $result[$m->key] = json_decode($m->value);
        }
        return ArrayHelper::merge($defaults, $result);
    }
}