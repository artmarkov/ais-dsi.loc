<?php

namespace common\widgets\qrcode\formatter;

use yii\base\BaseObject;
use common\widgets\qrcode\formatter\IFormatter;

class Email extends BaseObject implements IFormatter {

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $subject;

    /**
     *
     * @var string
     */
    public $body;

    /**
     * 
     * @return string
     */
    public function format() {
        $content = 'mailto:' . $this->email . '?';
        if (!empty($this->subject)) {
            $content .= 'subject=' . urlencode($this->subject) . '&';
        }
        if (!empty($this->body)) {
            $content .= 'body=' . urlencode($this->body);
        }
        return $content;
    }

}
