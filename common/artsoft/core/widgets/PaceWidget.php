<?php

/**
 * 
 * add to main.php after beginBody() 
 * artsoft\widgets\PaceWidget::widget();
 * 
 */

namespace artsoft\widgets;

use yii\base\Widget;
use artsoft\widgets\assets\PaceAsset;

class PaceWidget extends Widget {

    public function run() {

        PaceAsset::register($this->view);

        return $this->render('pace');
    }

}
