<?php

namespace console\controllers;

use artsoft\helpers\ArtHelper;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use artsoft\models\User;
use common\models\employees\Employees;
use common\models\guidejob\Bonus;
use common\models\own\Department;
use common\models\parents\Parents;
use common\models\reports\ProgressReport;
use common\models\schedule\ConsultSchedule;
use common\models\students\Student;
use common\models\students\StudentDependence;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersConsult;
use common\models\teachers\TeachersSchedule;
use common\models\user\UserCommon;
use dosamigos\transliterator\TransliteratorHelper;
use yii\base\DynamicModel;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii document
 *
 * @author markov-av
 */
class DocumentController extends Controller
{
    protected $plan_year;

    public function actionIndex()
    {
        $this->plan_year = 2024;
        $this->stdout("\n");
//        $this->addProgress();
//        $this->addConsult();
//        $this->addSchedule();

    }


    public function addProgress()
    {
        $teachers_list = \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList();
        $model_date = new DynamicModel(['plan_year', 'teachers_id', 'is_history']);
        $model_date->addRule(['plan_year', 'teachers_id', 'is_history'], 'integer');
        $model_date->plan_year = $this->plan_year;

//        $t=0;
        foreach ($teachers_list as $teachers_id => $teachers_fio) {
//            if($t == 0) {
//                if ($teachers_fio == 'Майстренко Е.А.') {
//                    $t = 1;
//                }
//                continue;
//            }
            $model_date->teachers_id = $teachers_id;
            $model_date->is_history = true;
            $models = new ProgressReport($model_date);
            $models->saveXls();
            $models->makeDocument();
            $models->cliarTemp();
            $this->stdout('Добавлен документ для : ' . $teachers_fio . " ", Console::FG_GREY);
            $this->stdout("\n");
        }
    }

    public function addSchedule()
    {
        $teachers_list = \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList();
        $model_date = new DynamicModel(['plan_year', 'teachers_id']);
        $model_date->addRule(['plan_year', 'teachers_id'], 'integer');
        $model_date->plan_year = $this->plan_year;

//        $t=0;
        foreach ($teachers_list as $teachers_id => $teachers_fio) {
//            if($t == 0) {
//                if ($teachers_fio == 'Майстренко Е.А.') {
//                    $t = 1;
//                }
//                continue;
//            }
            $model_date->teachers_id = $teachers_id;
            $models = new TeachersSchedule($model_date);
            $models->saveXls();
            $models->makeDocument();
            $models->cliarTemp();
            $this->stdout('Добавлен документ для : ' . $teachers_fio . " ", Console::FG_GREY);
            $this->stdout("\n");
        }
    }

    public function addConsult()
    {
        $teachers_list = \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList();
        $model_date = new DynamicModel(['plan_year', 'teachers_id']);
        $model_date->addRule(['plan_year', 'teachers_id'], 'integer');
        $model_date->plan_year = $this->plan_year;

//        $t=0;
        foreach ($teachers_list as $teachers_id => $teachers_fio) {
//            if($t == 0) {
//                if ($teachers_fio == 'Майстренко Е.А.') {
//                    $t = 1;
//                }
//                continue;
//            }
            $model_date->teachers_id = $teachers_id;
            $models = new TeachersConsult($model_date);
            $models->saveXls();
            $models->makeDocument();
            $models->cliarTemp();
            $this->stdout('Добавлен документ для : ' . $teachers_fio . " ", Console::FG_GREY);
            $this->stdout("\n");
        }
    }

}
