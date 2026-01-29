<?php

namespace console\controllers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\education\LessonItems;
use common\models\reports\ProgressReport;
use Yii;
use yii\base\DynamicModel;
use yii\console\Controller;
use artsoft\helpers\Html;
use common\models\planfix\Planfix;
use common\models\user\UsersView;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii test
 *
 * @author markov-av
 */
class TestController extends Controller
{
    public function actionIndex()
    {
//        $model_date = new DynamicModel(['plan_year', 'teachers_id', 'is_history']);
//        $model_date->addRule(['plan_year', 'teachers_id',  'is_history'], 'integer');
//        $model_date->plan_year = ArtHelper::getStudyYearDefault();
//        $model_date->teachers_id = 1015;
//        $model_date->is_history = true;
//        $models = new ProgressReport($model_date);
//        $models->saveXls();
//        $models->makeDocument();
        ini_set('memory_limit', '1024M');
        $models = (new Query())->from('lesson_items_progress_studyplan_view')
            ->where(['subject_sect_studyplan_id' => 0])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['plan_year' => 2025])
            ->all();
        foreach ($models as $model) {
            $modelLesson = LessonItems::findOne(['id' => $model['lesson_items_id'], 'teachers_id' => NULL]);
            if($modelLesson && $model['teachers_id']) {
                $teachers_id = RefBook::find('users_teachers')->getValue($model['created_by']);
                if($teachers_id == $modelLesson->teachers_id) {
                $modelLesson->teachers_id = $model['teachers_id'];
                $modelLesson->save(false);
                $this->stdout('Îê: ' . $modelLesson->id, Console::FG_BLUE);
                $this->stdout("\n");

                }
            } else {
                $this->stdout('Îøèáêà: ' . $model['lesson_items_id'], Console::FG_PURPLE);
                $this->stdout("\n");
            }
        }
    }

}
