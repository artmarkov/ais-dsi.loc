<?php

namespace backend\widgets;

use yii\base\Widget;

class WorksAuthorWidget extends Widget {
   
    public $model;

    public function run() {
  
        return $this->render('worksAuthor', [
                    'model' => $this->model
                    
        ]);
    }

}
