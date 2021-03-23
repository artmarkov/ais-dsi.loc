<?php

namespace common\widgets\qrcode\formatter;

use yii\base\BaseObject;
use common\widgets\qrcode\formatter\IFormatter;

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
