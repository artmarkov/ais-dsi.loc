<?php

namespace frontend\components;

use yii\captcha\CaptchaAction as DefaultCaptchaAction;

class NumericCaptcha extends DefaultCaptchaAction
{
    public $backColor = 0xffffff;
    public $foreColor = 0x2a5bc7;
    public $width = 100;
    public $height = 40;
    public $padding = 4;
    public $offset = 5;

    protected function generateVerifyCode()
    {
        $length = 5;
        $digits = '0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $digits[mt_rand(0, 9)];
        }
        return $code;
    }
}