<?php

namespace common\widgets\qrcode\widgets;

use Yii;
use common\widgets\qrcode\widgets\Text;
use common\widgets\qrcode\formatter\Smsphone as FormatSms;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class Smsphone extends Text {

    public $phone;

    public function run() {

        $formatter = new FormatSms([
            'phone' => $this->phone,
        ]);
        $this->text = $formatter->format();
        return parent::run();
    }

}
