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
use artsoft\helpers\Html;

class ScrollupWidget extends Widget {

    public function run() {

        ScrollupAsset::register($this->view);

        return Html::a('', '#', ['class' => 'scrollup', 'title' => 'Наверх']);
    }

}
