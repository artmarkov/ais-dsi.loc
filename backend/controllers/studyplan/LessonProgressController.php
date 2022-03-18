<?php

namespace backend\controllers\studyplan;

use common\models\education\LessonProgress;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\helpers\Json;

/**
 * LessonProgressController implements the CRUD actions for common\models\education\LessonProgress model.
 */
class LessonProgressController extends BaseController 
{
    public $modelClass       = 'common\models\education\LessonProgress';
    public $modelSearchClass = 'common\models\education\search\LessonProgressSearch';

    /**
     * Установка оценки
     * @return string|null
     * @throws \yii\db\Exception
     */
    public function actionSetMark()
    {
        $lesson_progress_id = $_GET['lesson_progress_id'];

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['lesson_mark_id'])) {
                $model = LessonProgress::findOne($lesson_progress_id);
                $model->lesson_mark_id = $_POST['lesson_mark_id'];
                $model->save(false);
                return Json::encode(['output' => $model->lesson_mark_id, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }
}