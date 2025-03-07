<?php

namespace console\jobs;

use artsoft\dbmanager\models\Db;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class FlushCache.
 */
class MakeDump extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $model = new Db();
        $path = '@frontend/web/db/';
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        if (file_exists($path)) {
            if (is_dir($path)) {
                if (is_writable($path)) {
                    $fileName = 'dump_' . date('Y_m_d_H_i_s') . '.pgsql';
                    $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
                    $db = Yii::$app->getDb();
                    if ($db) {
                        $command = $model->makeExportComand($db, $filePath);
                        // print_r($command); die();
                        exec($command);
                    }
                }
            }
        }
    }
}
