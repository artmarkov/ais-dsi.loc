<?php
/**
 * Информация о дате создания, дате изменения, кто создал, кто изменил запись
 */
namespace artsoft\widgets;

use yii\base\Widget;

class InfoModel extends Widget
{
    public $model;

    public function run()
    {
        return $this->render('infomodel', ['model' => $this->model]);
    }
}
