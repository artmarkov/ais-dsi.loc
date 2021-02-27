<?php

namespace backend\controllers\service;

use backend\controllers\DefaultController;
use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use common\models\service\ImageManager;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
/**
 * Description of ImageManagerController
 *
 * @author markov-av
 */
class ImageManagerController extends DefaultController {

    /**
     * 
     * @return type
     * @throws BadRequestHttpException
     */
    public function actionFileUpload() {
        
        $type_array = [ 
              'jpg' => ['type' => 'image'],
              'png' => ['type' => 'image', 'filetype' => 'image/png'],
              'pdf' => ['type' => 'pdf'],
              'mp4' => ['type' => 'video' , 'filetype' => 'video/mp4'],        
          ];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $dir = Yii::getAlias('@images') . '/' . $post['ImageManager']['class'] . '/';
            
            if (!file_exists($dir)) {
                FileHelper::createDirectory($dir);
            }

            $result_link = str_replace('admin', '', Url::home(true)) . 'uploads/images' . '/' . $post['ImageManager']['class'] . '/';
            $file = UploadedFile::getInstanceByName('ImageManager[attachment]');
            $model = new ImageManager();

            $model->name = strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $file->extension;
            
            $model->type = ArrayHelper::getValue($type_array, $file->extension . '.type') ? ArrayHelper::getValue($type_array, $file->extension . '.type') : 'image';
            $model->filetype = ArrayHelper::getValue($type_array, $file->extension . '.filetype');
            $model->url = $result_link . $model->name;
            $model->size = $file->size;
           // echo '<pre>' . print_r($model, true) . '</pre>';
            $model->load($post);

            $model->validate();
            if ($model->hasErrors()) {
                $result = [
                    'error' => $model->getFirstError('file')
                ];
            } else {
                if ($file->saveAs($dir . $model->name)) {
//                    $imag = Yii::$app->image->load($dir . $model->name);
//                    $imag->resize(800, NULL, Image::PRECISE)->save($dir . $model->name, 85);
                    $result = ['filelink' => $result_link . $model->name, 'filename' => $model->name];
                } else {
                    $result = [
                        'error' => Yii::t('art', 'Error')
                    ];
                }
                $model->save();
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        } else {

            throw new BadRequestHttpException('Only POST is allowed.');
        }
    }

    /**
     * 
     * @param type $id
     * @return boolean
     * @throws MethodNotAllowedHttpException
     */
    public function actionSortImage($id) {
        if (Yii::$app->request->isAjax) {
            
            $model = ImageManager::findOne(['item_id' => $id]);
            $post = Yii::$app->request->post('sort');
            
            if ($post['oldIndex'] > $post['newIndex']) {
                $param = ['and', ['>=', 'sort', $post['newIndex']], ['<', 'sort', $post['oldIndex']]];
                $counter = 1;
            } else {
                $param = ['and', ['<=', 'sort', $post['newIndex']], ['>', 'sort', $post['oldIndex']]];
                $counter = -1;
            }
            ImageManager::updateAllCounters(['sort' => $counter], [
                'and', ['class' => $model->class, 'item_id' => $id], $param]);
            ImageManager::updateAll(['sort' => $post['newIndex']], [
                'id' => $post['stack'][$post['newIndex']]['key']
            ]);

            return true;
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * 
     * @return boolean
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage() {
        if (($model = ImageManager::findOne(Yii::$app->request->post('key'))) and $model->delete()) {

            return true;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
