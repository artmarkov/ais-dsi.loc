<?php

namespace xj\qrcode\formatter;

use yii\base\BaseObject;
use xj\qrcode\formatter\IFormatter;

class Smsphone extends BaseObject implements IFormatter {

    /**
     *
     * @var string PhoneNumber
     */
    public $phone;

    /**
     * 
     * @return string
     */
    public function format() {
        return 'sms:' . $this->phone;
    }

}
