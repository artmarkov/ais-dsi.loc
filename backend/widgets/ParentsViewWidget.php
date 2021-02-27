<?php

namespace backend\widgets;

use yii\base\Widget;

class ParentsViewWidget extends Widget {

    public $model;

    public function run() {

        return $this->render('parentsView', [
                    'model' => $this->model,
        ]);
    }

}
