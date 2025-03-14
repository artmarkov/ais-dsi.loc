<?php

namespace artsoft\dbmanager\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class Db extends Model
{
    public function makeExportComand($db, $filePath)
    {
        return sprintf("PGPASSWORD='%s' pg_dump -h %s -p 5432 -U %s --disable-triggers -d %s > %s",
            $db->password,
            $this->getDsnAttribute('host', $db->dsn),
            $db->username,
            $this->getDsnAttribute('dbname', $db->dsn),
            $filePath);
    }

    public function getFilesAllSize($files)
    {
        $data = ArrayHelper::getColumn($this->getFilesData($files), 'file_size');
        return Yii::$app->formatter->asSize(array_sum($data));
    }

    public function getFilesData($files)
    {
        $arr = array();
        foreach ($files as $key => $file) {
            $arr[] = array(
                'dump' => $file,
                'file_size' => filesize($file),
                'size' => Yii::$app->formatter->asSize(filesize($file)),
                'create_at' => Yii::$app->formatter->asDatetime(filectime($file), 'php:Y-m-d h:i:s'),
                'type' => pathinfo($file, PATHINFO_EXTENSION),
            );
        }
        return $arr;
    }

    public function getFiles($files)
    {
        Yii::$app->params['count_db'] = count($files);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $this->getFilesData($files),
            'sort' => [
                'attributes' => ['dump', 'size', 'create_at'],
                'defaultOrder' => [
                    'create_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);
        return $dataProvider;
    }

    public function import($path)
    {
        if (file_exists($path)) {
            $path = \yii\helpers\Html::encode($path);
            $db = Yii::$app->getDb();
            if (!$db) {
                Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'No database connection.'));
            }
            $db->password = str_replace("(", "\(", $db->password);
            $command = "psql -u'" . $db->username . "' " . $this->getDsnAttribute('dbname', $db->dsn) . " -p'" . $db->password . "' < " . $path;
            //$command = "pgsql -u'" . $db->username . "' " . $this->getDsnAttribute('dbname', $db->dsn) . " -p'" . $db->password . "' < " . $path;
            //$command = 'pgsql --host=' . $this->getDsnAttribute('host', $db->dsn) . ' --user=' . $db->username . ' --password=' . $db->password . ' ' . $this->getDsnAttribute('dbname', $db->dsn) . ' < ' . $path;
            //[2025-03-05 15:26:50] "/Applications/pgAdmin 4.app/Contents/SharedSupport/pg_dump" --dbname=ais_dshi_13062023 --file=/Users/markov-av/dump_ais_dshi_05-03-2025 --format=t --clean --create --if-exists --username=postgres --host=45.12.75.11 --port=5432
            // exec($command);
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', "Dump {path} successfully imported.", ['path' => $path]));
        } else {
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The specified path does not exist.'));
        }
        return Yii::$app->response->redirect(['dbmanager/default/index']);
    }


    public function export($path = null)
    {
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        if (file_exists($path)) {
            if (is_dir($path)) {
                if (!is_writable($path)) {
                    Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'Directory is not writable.'));
                    return Yii::$app->response->redirect(['dbmanager/default/index']);
                }
                $fileName = 'dump_' . date('Y_m_d_H_i_s') . '.pgsql';
                $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
                $db = Yii::$app->getDb();
                if (!$db) {
                    Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'No database connection.'));
                    return Yii::$app->response->redirect(['dbmanager/default/index']);
                }
                //Экранируем скобку которая есть в пароле
               // $db->password = str_replace("(", "\(", $db->password);
                $command = $this->makeExportComand($db, $filePath);
               // print_r($command); die();
                exec($command);
                Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', "Export completed successfully. File {fileName} in the {path} folder.", ['fileName' => $fileName, 'path' => $path]));
            } else {
                Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The path must be a folder.'));
            }
        } else {
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The specified path does not exist.'));
        }
        return Yii::$app->response->redirect(['dbmanager/default/index']);
    }

    //Возвращает название хоста (например localhost)
    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public function delete($path)
    {
        if (file_exists($path)) {
            $path = \yii\helpers\Html::encode($path);
            unlink($path);
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The database dump removed.'));
        } else {
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The specified path does not exist.'));
        }
        return Yii::$app->response->redirect(['dbmanager/default/index']);
    }

    public function download($path)
    {
        if (file_exists($path)) {
            $path = \yii\helpers\Html::encode($path);
            return Yii::$app->response->sendFile($path);
        } else {
            Yii::$app->session->setFlash('info', Yii::t('art/dbmanager', 'The specified path does not exist.'));
        }
        return Yii::$app->response->redirect(['dbmanager/default/index']);
    }

}