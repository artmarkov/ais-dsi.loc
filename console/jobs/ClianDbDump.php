<?php

namespace console\jobs;

use Yii;
use yii\helpers\FileHelper;

/**
 * Class ClianDbDump.
 */
class ClianDbDump extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $path = '@frontend/web/db/';
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        $files = FileHelper::findFiles($path, ['only' => ['*.tar'], 'recursive' => FALSE]);
        $shelf_life_dbdump = Yii::$app->settings->get('module.shelf_life_dbdump', 60);
        $datetime = time() - $shelf_life_dbdump * 86400;
        foreach ($files as $key => $file) {
            if ($datetime > filectime($file)) {
                if (file_exists($file)) {
                    $file = \yii\helpers\Html::encode($file);
                    unlink($file);
                }
            }
        }
    }

}
