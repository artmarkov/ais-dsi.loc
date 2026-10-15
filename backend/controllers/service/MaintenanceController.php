<?php

namespace backend\controllers\service;


class MaintenanceController extends \backend\controllers\DefaultController
{
    /**
     * backend/config/main.php снять //
     * @return mixed|string
     */
    public function actionIndex()
    {
        return $this->renderPartial('index', [
            'message' => 'На сайте в разделе администратора ведутся технические работы. Скоро всё восстановим.',
            'retry_after' => 300 // секунд
        ]);
    }
}