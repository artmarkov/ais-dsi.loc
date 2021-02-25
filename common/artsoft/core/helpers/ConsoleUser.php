<?php

namespace artsoft\helpers;

class ConsoleUser extends \yii\web\User
{
    public $autoUserIdentityId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->enableAutoLogin = false;
        $this->enableSession = false;
        parent::init();

        $identity = call_user_func([$this->identityClass, 'findIdentity'], $this->autoUserIdentityId);
        if (null !== $identity) {
            $this->setIdentity($identity);
        }
    }

}