<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\teachers\TeachersPlan;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run console command:  php yii teachersplan
 *
 * @author markov-av
 */
class TeachersplanController extends Controller
{
    public function actionIndex()
    {
        $this->addPlan();
    }

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function addPlan()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/teachers_plan.xlsx');
        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue; // skip header
                }
//                if ($i > 15) {
//                    continue; // skip header
//                }
                /* @var $row Row */
                $v = $row->toArray();
                // print_r($v);

                foreach ([1000, 1001] as $direction_id) {
                    try {
                        if ($direction_id == 1000) {
                            $teachers_id = $this->findByTeachers2($v[2]);
                        } else {
                            $teachers_id = $this->findByTeachers2($v[3]);
                        }
                        if ($teachers_id) {
                            $model = new TeachersPlan();
                            $model->direction_id = $direction_id;
                            $model->teachers_id = $teachers_id;
                            $model->plan_year = $v[1];
                            $model->half_year = 0;
                            $model->week_num = null;
                            $model->week_day = $v[4];
                            $model->time_plan_in = date("H:i", $v[5]);
                            $model->time_plan_out = date("H:i", $v[6]);
                            $model->auditory_id = $this->findByAuditoryNum($v[7]) ?? null; //Аудитория
                            if ($model->save(false)) {
                                $this->stdout('Добавлен план: ' . $model->id, Console::FG_GREY);
                                $this->stdout("\n");
                            }
                        }
                    } catch (\Exception $e) {
                        $this->stdout('Ошибка: ' . $v[0], Console::FG_PURPLE);
                        $this->stdout("\n");
                    }
                }
            }
        }
    }

    public function findByAuditoryNum($num)
    {
        $auditory = \Yii::$app->db->createCommand('SELECT id 
                                                    FROM auditory 
                                                    WHERE num=:num',
            [
                'num' => $num
            ])->queryOne();
        return $auditory['id'];
    }


    public function findUserByTeachers($full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT user_id 
                                                    FROM teachers_view 
                                                    WHERE fullname=:fullname 
                                                   ',
            [
                'fullname' => $full_name,
            ])->queryOne();
        return $user['user_common_id'] ?? false;
    }

    public function findByTeachers2($full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT teachers_id 
                                                    FROM teachers_view 
                                                    WHERE fullname=:fullname 
                                                   ',
            [
                'fullname' => $full_name,
            ])->queryOne();
        return $user['teachers_id'] ?? false;
    }

}