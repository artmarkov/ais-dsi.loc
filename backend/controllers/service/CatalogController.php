<?php

namespace backend\controllers\service;

use Yii;
use common\models\service\Catalog;
use backend\controllers\DefaultController;
use yii\web\NotFoundHttpException;


/**
 * CatalogController implements the CRUD actions for Catalog model.
 */
class CatalogController extends DefaultController
{
    public function actionMove($item, $action, $second)
    {
        $item_model = Catalog::findOne($item);
        $second_model = Catalog::findOne($second);
        switch ($action){
            case 'after':
                $item_model->insertAfter($second_model);
                break;
            case 'before':
                $item_model->insertBefore($second_model);
                break;
            case 'over':
                $item_model->appendTo($second_model);
                break;
        }
        // echo '<pre>' . print_r($item_model, true) . '</pre>';
        $item_model->save();
        
        return true;
    }

   

    /**
     * Creates a new Catalog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   
    public function actionCreate()
    {
    /** @var  $model Menu|NestedSetsBehavior */
   $model = new Catalog();

    //Поиск корневого элемента
    $root = $model->find()->where(['depth' => '0'])->one();

    if ($model->load(Yii::$app->request->post())) {
       // echo '<pre>' . print_r($model, true) . '</pre>';
        //Если нет корневого элемента (пустая таблица)
        if (!$root) {
            /** @var  $rootModel Menu|NestedSetsBehavior */
            $rootModel = new Catalog(['name' => 'root', 'url' => '/']);
            $rootModel->makeRoot(); //делаем корневой
            $model->appendTo($rootModel);
        } else {
            $model->appendTo($root); //вставляем в конец корневого элемента
        }

        if ($model->save()){
            return $this->redirect('index');
        }
    }

    return $this->renderAjax('create', [
        'model' => $model,
    ]);
}
/**
 * 
 * @param type $id
 * @return type
 */
public function actionIndex($id = 1) 
{
    return $this->render('index', ['data' => Catalog::findOne($id)->tree()]);
}
    /**
     * Updates an existing Catalog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Catalog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Catalog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('art/service', 'The requested page does not exist.'));
    }
}
