<?php

namespace artsoft\dbmanager\controllers;

use artsoft\controllers\admin\BaseController;
use Yii;
use yii\helpers\FileHelper;
use artsoft\dbmanager\models\Db;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;


class DefaultController extends BaseController {        
    
    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('dbmanager');
    }
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
        ]);
    }
    
    public function actionIndex($path = null){         
        $path = FileHelper::normalizePath(Yii::getAlias($this->getModule()->dumpPath));
        $files = FileHelper::findFiles($path, ['only' => ['*.pgsql'], 'recursive' => FALSE]);
        $model = new Db();
        $dataProvider = $model->getFiles($files);
       
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionImport($path) {
        $model = new Db();
        $model->import($path);
    }
    
    public function actionExport($path = null) {
        $path = $path ? : $this->getModule()->dumpPath;
        $model = new Db();
        $model->export($path);
    }
    
    public function actionDelete($path) {
        $model = new Db();
        $model->delete($path);
    }
    
    public function actionDownload($path)
    {
        $model = new Db();
        $model->download($path);
    }
    
    public function actionDeleteAll()
    {
        $fail = [];
        $path = FileHelper::normalizePath(Yii::getAlias($this->getModule()->dumpPath));
        $files = FileHelper::findFiles($path, ['only' => ['*.pgsql'], 'recursive' => FALSE]);
        foreach ($files as $key => $file)
        {
            $path = \yii\helpers\Html::encode($file);

            if (file_exists($path))
            {
                if (!unlink($path))
                {
                    $fail[] = $file;
                }
            }
        }
        if (empty($fail))
        {
            Yii::$app->session->setFlash('crudMessage', Yii::t('art/dbmanager', 'All dumps successfully removed.'));
        }
        else
        {
            Yii::$app->session->setFlash('crudMessage', Yii::t('art/dbmanager', 'Error deleting dumps.'));
        }
        return Yii::$app->response->redirect(['dbmanager/default/index']);
    }

}