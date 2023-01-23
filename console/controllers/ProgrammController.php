<?php

namespace console\controllers;

use artsoft\fileinput\models\FileManager;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\activities\ActivitiesOver;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\education\EducationProgrammLevelSubject;
use common\models\efficiency\TeachersEfficiency;
use common\models\own\Department;
use common\models\schedule\SubjectSchedule;
use common\models\schoolplan\Schoolplan;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\Subject;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run console command:  php yii programm
 *
 * @author markov-av
 */
class ProgrammController extends Controller
{
    public $err = [];

    public function actionIndex()
    {
        $this->stdout("\n");
//        $this->addProgramm();
        $this->addStudyplan();
       // print_r(array_unique($this->err));
    }

    public function addStudyplan()
    {
        /*       [1186] => Array
          (
              [plan_id] => 595
                  [plan_year] => 2022
                  [course] => 1
                  [letter] => 01
                  [period_study] => 1
                  [unit_name] => Художественное отделение
          [dep_name] => Художественный отдел
          [type_training_const] => 0
                  [student_fio] => Девкина Юлия Владимировна
          [pupils_sub] => Array
          (
              [0] => Array
              (
                  [cat_name] => Дисциплины отдела
                  [sub_name] => Живопись
                  [vid_id] => 2
                  [group_name] => 1||1||01
                  [type_training] => 0
                  [week_time] => 2
                  [year_time] => 0
                  [teach] => Жбан Людмила Владимировна
                  [week_time_teach] => 2
                  [accomp] =>
                  [week_time_accomp] =>
                  [schedule] => Array
                                          (
                                              [0] => Array
                                              (
                                                  [week_num] => 0
                                                  [week_day] => 4
                                                  [time_in] => 18:00
                                                  [time_out] => 19:30
                                                  [auditory_number] => 3
                                              )

                                      )

                              )

                      )

              )*/


        // ini_set('memory_limit', '2024M');
        $f = file_get_contents('data/studyplan.txt');
        $data = json_decode($f, true);
        //  print_r($data);
        foreach ($data as $i => $d) {
            if ($d['plan_year'] != '2022') {
                continue;
            }
            if (!$this->findByStudent($d['student_fio'])) {
                continue;
            }
            // print_r($d);
            $model_programm = EducationProgramm::findOne($this->getProgrammId($d['plan_id']));
            if ($model_programm) {
                try {
                    //  $transaction = \Yii::$app->db->beginTransaction();
                    $model_studyplan = Studyplan::find()->where(['=', 'programm_id', $model_programm->id])->andWhere(['=', 'plan_year', $d['plan_year']])->andWhere(['=', 'course', $d['course']])->andWhere(['=', 'student_id', $this->findByStudent($d['student_fio'])])->one() ?? new Studyplan();
                    $model_studyplan->programm_id = $model_programm->id;
                    $model_studyplan->plan_year = $d['plan_year'];
                    $model_studyplan->student_id = $this->findByStudent($d['student_fio']);
                    $model_studyplan->course = $d['course'];
                    $model_studyplan->subject_type_id = $this->getSubjectType($d['type_training_const']);
                    if (!($flag = $model_studyplan->save(false))) {
                        //  $transaction->rollBack();
                        break;
                    }
                    foreach ($d['pupils_sub'] as $ii => $dd) {
                        // print_r($dd);
                        $model_subject = StudyplanSubject::find()->where(['=', 'studyplan_id', $model_studyplan->id])->andWhere(['=', 'subject_cat_id', $this->getSubjectCat($dd['cat_name'], $dd['sub_name'])])->andWhere(['=', 'subject_id', $this->getSubject($dd['sub_name'])])->one() ?? new StudyplanSubject();
                        $model_subject->studyplan_id = $model_studyplan->id;
                        $model_subject->subject_cat_id = $this->getSubjectCat($dd['cat_name'], $dd['sub_name']);
                        $model_subject->subject_id = $this->getSubject($dd['sub_name']);
                        $model_subject->subject_type_id = $this->getSubjectType($dd['type_training']);
                        $model_subject->subject_vid_id = $this->getSubjectVid2($dd['vid_id']);
                        $model_subject->week_time = $dd['week_time'];
                        $model_subject->year_time_consult = $dd['year_time'];

                        if (!($flag = $model_subject->save(false))) {
                            // $transaction->rollBack();
                            break;
                        }

//                        $model_load = TeachersLoad::find()->where(['=', 'subject_sect_studyplan_id', $model_studyplan->id])->andWhere
//                        $model_schedule = SubjectSchedule::find()->where(['=', 'studyplan_id', $model_studyplan->id])->andWhere
                    }
                } catch (\Exception $e) {
                    // $transaction->rollBack();
                    $this->stdout('Error ' . $i . "-" . $ii . " ", Console::FG_RED);
                    $this->stdout("\n");
                    //  print_r($d);
                }
            } else {
                $this->stdout('Не найдена программа: ' . $d['plan_id'] . " ", Console::FG_RED);
                $this->stdout("\n");
            }
        }
    }

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function addProgramm()
    {
        $f = file_get_contents('data/programm.txt');
        $data = json_decode($f, true);

        foreach ($data as $i => $d) {
            if ($d['plan_year'] != '2022') {
                continue;
            }
            // print_r($d);
            $model_programm = EducationProgramm::findOne($this->getProgrammId($d['plan_id']));
            if ($model_programm) {
                foreach ($d['study_sub'] as $ii => $dd) {
                    foreach ($dd['time'] as $iii => $ddd) {
                        try {
                            $transaction = \Yii::$app->db->beginTransaction();
                            $model_level = EducationProgrammLevel::find()->where(['=', 'programm_id', $model_programm->id])->andWhere(['=', 'course', $ddd['course']])->one() ?? new EducationProgrammLevel();
                            $model_level->programm_id = $model_programm->id;
                            $model_level->course = $ddd['course'];
                            if (!($flag = $model_level->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                            $model_subject = EducationProgrammLevelSubject::find()->where(['=', 'programm_level_id', $model_level->id])->andWhere(['=', 'subject_cat_id', $this->getSubjectCat($dd['cat_name'], $dd['sub_name'])])->andWhere(['=', 'subject_id', $this->getSubject($dd['sub_name'])])->one() ?? new EducationProgrammLevelSubject();
                            $model_subject->programm_level_id = $model_level->id;
                            $model_subject->subject_cat_id = $this->getSubjectCat($dd['cat_name'], $dd['sub_name']);
                            $model_subject->subject_vid_id = $this->getSubjectVid($dd['sub_name']);
                            $model_subject->subject_id = $this->getSubject($dd['sub_name']);
                            $model_subject->week_time = $ddd['week_time'];
                            $model_subject->year_time_consult = $ddd['year_time'];

                            if (!($flag = $model_subject->save(false))) {
                                $transaction->rollBack();
                                break;
                            } else {
                                $transaction->commit();
                            }
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            $this->stdout('Error ' . $i . "-" . $ii . "-" . $iii . " ", Console::FG_RED);
                            $this->stdout("\n");
                        }
                    }
                }
            } else {
                $this->stdout('Не найдена программа: ' . $d['plan_id'] . " ", Console::FG_RED);
                $this->stdout("\n");
            }
        }
    }

    public function getSubjectType($name)
    {
        $position_id = null;
        switch ($name) {
            case '0' :
                $position_id = 1000;
                break;
            case '1' :
                $position_id = 1001;
                break;

        }
        return $position_id;
    }

    public function getSubjectVid2($name)
    {
        $position_id = null;
        switch ($name) {
            case '0' :
                $position_id = 1000;
                break;
            case '1' :
                $position_id = 1002;
                break;
            case '2' :
                $position_id = 1001;
                break;

        }
        return $position_id;
    }

    public function getSubject($name)
    {
        $subject = \Yii::$app->db->createCommand('SELECT id 
                                                    FROM subject 
                                                    WHERE name=:name',
            [
                'name' => $this->getSubjectName($name)
            ])->queryOne();

        return $subject['id'] ?? null;
    }

    public function getSubjectName($name)
    {
        switch ($name) {
            case 'Основы изобразительной грамоты' :
                $name = 'Основы изобразительного искусства';
                break;
            case 'Основы мультипликации' :
                $name = 'Основы мультипликации и режиссуры анимационного кино';
                break;
            case 'Арт - практика' :
                $name = 'Арт-практика';
                break;
            case 'Основы изобрвзительной деятельности и дизайн костюма' :
                $name = 'Основы изобразительной деятельности и дизайна одежды';
                break;
            case 'Балетная гимнастика' :
                $name = 'Баллетная гимнастика';
                break;
            case 'Современная хореография' :
                $name = 'Современный танец';
                break;
            case 'Музыкальный инструмент - Фортепиано' :
                $name = 'Фортепиано';
                break;
            case 'Композиция (станковая, прикладная)' :
                $name = 'Композиция станковая';
                break;
            case 'Body Ballet' :
                $name = 'Боди-балет';
                break;
            case 'Слушание музыки' :
                $name = 'Слушание музыки';
                break;
            case 'Эстрадно-джазовый оркестр' :
            case 'Оркестр гитаристов' :
            case 'Народный оркестр' :
            case 'Оркестр духовых инструментов' :
                $name = 'Оркестровый класс';
                break;
            case 'Ансамбль народных инструментов' :
            case 'Струнный ансамбль' :
                $name = 'Ансамбль';
                break;
        }

        return $name;
    }

    public function getSubjectVid($name)
    {
        $subject = \Yii::$app->db->createCommand('SELECT vid_list 
                                                    FROM subject 
                                                    WHERE name=:name',
            [
                'name' =>  $this->getSubjectName($name)
            ])->queryOne();
        $sub = explode(',', $subject['vid_list']);
        return $sub[0] ?? null;
    }

    public function getSubjectCat($name, $sub_name)
    {
        $cat_id = null;
        $sub_name = $this->getSubjectName($sub_name);
        switch ($name) {
            case 'Дисциплины отдела' :
            case 'Общие дисциплины школы' :
            case 'Коллектив' :
            case 'Предмет по выбору' :
            case 'Сводные репетиции' :
                if (Subject::find()->where(['=', 'name', $sub_name])->andWhere(['like', 'category_list', '1001'])->exists()) {
                    $cat_id = 1001;
                } elseif (Subject::find()->where(['=', 'name', $sub_name])->andWhere(['like', 'category_list', '1002'])->exists()) {
                    $cat_id = 1002;
                }
                break;
            case 'Специальность' :
                $cat_id = 1000;
                break;
            case 'Инструмент' :
                $cat_id = 1003;
                break;
        }
        return $cat_id;
    }

    public function getProgrammId($id)
    {
        $ids = [
            569 => 1000, 568 => 1001, 567 => 1002, 586 => 1003, 584 => 1004, 587 => 1005, 585 => 1006, 588 => 1007, 574 => 1008,
            576 => 1009, 578 => 1010, 570 => 1011, 573 => 1012, 557 => 1013, 591 => 1015, 594 => 1016, 589 => 1017, 592 => 1018,
            590 => 1019, 593 => 1020, 575 => 1021, 577 => 1022, 579 => 1023, 571 => 1024, 573 => 1025, 582 => 1026, 583 => 1027,
            580 => 1028, 581 => 1029, 556 => 1030, 559 => 1032, 537 => 1034, 542 => 1035, 540 => 1036, 534 => 1037, 552 => 1038,
            544 => 1039, 538 => 1040, 543 => 1041, 547 => 1042, 550 => 1043, 546 => 1044, 549 => 1045, 551 => 1046, 554 => 1047,
            555 => 1048, 563 => 1049, 539 => 1051, 545 => 1052, 558 => 1053, 548 => 1053, 565 => 1055,
        ];

        return $ids[$id] ?? null;
    }

    public function findByStudent($full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT students_id 
                                                    FROM students_view 
                                                    WHERE fullname=:fullname ',
            [
                'fullname' => $this->lat2cyr($full_name)
            ])->queryOne();
        return $user['students_id'] ?? false;
    }

    protected function lat2cyr($text)
    {
        $arr = array(
            'A' => 'А',
            'a' => 'а',
            'B' => 'В',
            'C' => 'С',
            'c' => 'с',
            'E' => 'Е',
            'e' => 'е',
            'H' => 'Н',
            'K' => 'К',
            'k' => 'к',
            'M' => 'М',
            'm' => 'м',
            'n' => 'п',
            'O' => 'О',
            'o' => 'о',
            'P' => 'Р',
            'p' => 'р',
            'T' => 'Т',
            'X' => 'Х',
            'x' => 'х'
        );
        return strtr($text, $arr);
    }
}