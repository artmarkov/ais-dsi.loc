<?php

/**
 * 
 * add to main.php before endBody() 
 * artsoft\widgets\ScrollupWidget::widget();
 * 
 */

namespace artsoft\widgets;

use yii\base\Widget;
use artsoft\widgets\assets\ScrollupAsset;

class ScrollupWidget extends Widget {

    public function run() {

        ScrollupAsset::register($this->view);

        return $this->render('scrollup');
    }

}
