<?php

namespace common\widgets\qrcode\widgets;

use common\widgets\qrcode\QRcode;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class Link extends Text
{

    protected function getLocal()
    {
        $outputDir = $this->getOutputDir();
        $filename = $this->getFilename();
        $saveFilename = $outputDir . '/' . $filename;
        if (false === file_exists($saveFilename)) {
            QRcode::init($this->qrOptions);
            //save file
            QRcode::png($this->text, $saveFilename, $this->ecLevel, $this->size, $this->margin);
        }
        return $saveFilename;
    }

}
