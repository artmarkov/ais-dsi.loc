<?php

namespace common\widgets\qrcode\formatter;

interface IFormatter {

    /**
     * Format Data
     * @return string
     */
    public function format();
}
