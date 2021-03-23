<?php

namespace common\widgets\qrcode\widgets;

use Yii;
use common\widgets\qrcode\widgets\Text;
use common\widgets\qrcode\formatter\Telphone as FormatTel;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class Telphone extends Text {

    public $phone;

    public function run() {

        $formatter = new FormatTel([
            'phone' => $this->phone,
        ]);
        $this->text = $formatter->format();
        return parent::run();
    }

}
