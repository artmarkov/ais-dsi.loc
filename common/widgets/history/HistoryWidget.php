<?php

namespace common\widgets\history;

use yii\base\Widget;

class HistoryWidget extends Widget
{
    public $data;

    public function run()
    {
        $dataProvider = $this->data->search(\Yii::$app->request->get());

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'filterModel' => $this->data,
        ]);
    }
}
