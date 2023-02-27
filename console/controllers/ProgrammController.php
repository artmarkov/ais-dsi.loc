<?php

namespace console\controllers;

use artsoft\fileinput\models\FileManager;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\Schedule;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\activities\ActivitiesOver;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\education\EducationProgrammLevelSubject;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\efficiency\TeachersEfficiency;
use common\models\own\Department;
use common\models\schedule\SubjectSchedule;
use common\models\schoolplan\Schoolplan;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\subject\Subject;
use common\models\subjectsect\SubjectSect;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\console\Controller;
use yii\db\Query;
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
      //  $this->addProgramm();
       // $this->generateGroup();
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

         ini_set('memory_limit', '2024M');
        $f = file_get_contents('data/studyplan.json');
        $data = json_decode($f, true);
//          print_r($data); die();
        foreach ($data as $i => $d) {
            if ($d['plan_year'] != '2022') {
                continue;
            }
//            if ($i > 100) {
//                break;
//            }
            if (!$this->findByStudent($d['student_fio'])) {
                $this->stdout('Не найден ученик: ' . $d['student_fio'] . " ", Console::FG_RED);
                $this->stdout("\n");
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
                        $model_subject->week_time = $dd['week_time_teach'];
                        $model_subject->year_time_consult = $dd['year_time'];

                        if (!($flag = $model_subject->save(false))) {
                            // $transaction->rollBack();
                            break;
                        }
                        if ($dd['vid_id'] == 0) {
                            $subject_sect_studyplan_id = 0;
                            $studyplan_subject_id = $model_subject->id;
                        } else {
                            $subject_sect_studyplan_id = $this->setSubjectSectStaudyplan($model_programm, $model_subject, $dd);
                            $studyplan_subject_id = 0;
                        }
//                        $this->setTeachersLoad($studyplan_subject_id, $subject_sect_studyplan_id, $dd);
//                        $this->setThematicPlans($studyplan_subject_id, $subject_sect_studyplan_id, $dd);
                        $this->setLessonProgress($studyplan_subject_id, $subject_sect_studyplan_id, $model_subject, $dd);
                    }
                } catch (\Exception $e) {
                    // $transaction->rollBack();
                    $this->stdout('Error ' . $d['student_fio'] . " - " . $dd['sub_name'] . " ", Console::FG_YELLOW);
                    $this->stdout("\n");
                    // print_r($d);
                }
            } else {
                $this->stdout('Не найдена программа: ' . $d['plan_id'] . " ", Console::FG_RED);
                $this->stdout("\n");
            }
        }
    }

    protected function setTeachersLoad($studyplan_subject_id, $subject_sect_studyplan_id, $dd)
    {
        foreach ([1000, 1001] as $direction_id) {
            if ($direction_id == 1000) {
                $teachers_id = $this->findByTeachers2($dd['teach']);
                $load_time = $dd['week_time_teach'];
                $load_time_consult = $dd['year_time'] != '' ? $dd['year_time'] : 0;
            } else {
                $teachers_id = $this->findByTeachers2($dd['accomp']);
                $load_time = $dd['week_time_accomp'];
                $load_time_consult = 0;
            }
       // print_r([$studyplan_subject_id, $subject_sect_studyplan_id,$direction_id,$teachers_id,$load_time,$load_time_consult]);
            if (!$teachers_id && !$load_time) {
                continue;
            }
            $model_load = TeachersLoad::find()
                    ->where(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])
                    ->andWhere(['=', 'studyplan_subject_id', $studyplan_subject_id])
                    ->andWhere(['=', 'direction_id', $direction_id])
                    ->andWhere(['=', 'teachers_id', $teachers_id])
                    ->one() ?? new TeachersLoad();

            $model_load->subject_sect_studyplan_id = $subject_sect_studyplan_id;
            $model_load->studyplan_subject_id = $studyplan_subject_id;
            $model_load->direction_id = $direction_id;
            $model_load->teachers_id = $teachers_id;
            $model_load->load_time = $load_time;
            $model_load->load_time_consult = $load_time_consult;
            if (!$model_load->save(false)) {
                return false;
            }

            foreach ($dd['schedule'] as $iii => $ddd) {
                // print_r( $ddd);
                if (($ddd['accomp_flag'] == 1 && $direction_id == 1001) || $direction_id == 1000) {
                    $model_schedule = (new Query())->from('subject_schedule')
                        ->where(['=', 'teachers_load_id', $model_load->id])
                        ->andWhere(['=', 'week_day', $ddd['week_day']])
                        ->andWhere(['=', 'time_in', Schedule::encodeTime($ddd['time_in'])])
                        ->andWhere(['=', 'time_out', Schedule::encodeTime($ddd['time_out'])])
                        ->andWhere(['=', 'auditory_id', $this->findByAuditoryNum($ddd['auditory_number'])]);
                    if ($ddd['week_num'] != 0) {
                        $model_schedule->andWhere(['=', 'week_num', $ddd['week_num']]);
                    }

                    if (!$model_schedule->exists()) {
                        $model_schedule = new SubjectSchedule();

                        $model_schedule->teachers_load_id = $model_load->id;
                        $model_schedule->week_num = $ddd['week_num'];
                        $model_schedule->week_day = $ddd['week_day'];
                        $model_schedule->time_in = $ddd['time_in'];
                        $model_schedule->time_out = $ddd['time_out'];
                        $model_schedule->auditory_id = $this->findByAuditoryNum($ddd['auditory_number']);
                        $model_schedule->save(false);
                    }
                }
            }
        }
        return true;
    }

    protected function setThematicPlans($studyplan_subject_id, $subject_sect_studyplan_id, $dd)
    {
        foreach ($dd['plan_sub'] as $half => $ddd) {
                $transaction = \Yii::$app->db->beginTransaction();
                $flag = true;
            try {
                $model = StudyplanThematic::find()
                    ->where(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])
                    ->andWhere(['=', 'studyplan_subject_id', $studyplan_subject_id])
                    ->one();
                if (!$model) {
                    $model = new StudyplanThematic();

                    $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
                    $model->studyplan_subject_id = $studyplan_subject_id;
                    $model->thematic_category = $ddd['items'][0]['category_id'] != '' ? 2 : 1;
                    $model->half_year = $half;
                    $model->doc_status = $ddd['confirm'];
                    $model->doc_sign_teachers_id = $this->findByTeachers2($ddd['confirm_name']);
                    $model->doc_sign_timestamp = $ddd['confirm_timestamp'];

                    if (!$model->save(false)) {
                        $transaction->rollBack();
                        break;
                    }
                    foreach ($ddd['items'] as $iii => $dddd) {
                        // print_r( $ddd);
                        $model_th = new StudyplanThematicItems();
                        $model_th->studyplan_thematic_id = $model->id;
                        $model_th->piece_category_id = $dddd['category_id'] != '' ? (integer)($dddd['category_id'] - 1 + 1000) : null;
                        $model_th->author = $dddd['author_name'] ?? null;
                        $model_th->piece_name = $dddd['piece_name'] ?? null;
                        $model_th->task = $dddd['mission'] ?? null;
                        if (!($flag = $model_th->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
//                    $this->stdout('Добавлен план: ' . $model->id . " ", Console::FG_GREY);
//                    $this->stdout("\n");
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->stdout('Ошибка добавления плана: ', Console::FG_PURPLE);
                $this->stdout("\n");
            }
        }
        return true;
    }

    protected function setSubjectSectStaudyplan($model_programm, $model_subject, $dd)
    {
        $d = explode('||', $dd['group_name']);
        $group_num = preg_replace("/[^\d]+/", '', $d[2]); // только числа
        $group_num = preg_replace("/^0+/", '', $group_num); // убираем ведущие нули

        if ($dd['vid_id'] == 1) { // $group['group_name'] . '||'. $group['group_name_dev'] . '||' . $group_num;
            // $group_name = $d[0];
            //  $group_name_dev = $d[1];
            $term_mastering = null;
            $course = null;

        } elseif ($dd['vid_id'] == 2) {// $course_sect . "||" . $period_study_sect.  "||" . $group['letter'];
            $course = $d[0] ?? null;
            $term_mastering = $d[1] ?? null;

        }

        $sect = SubjectSect::find()->where(new \yii\db\Expression("{$model_programm->id} = any (string_to_array(programm_list, ',')::int[])"))
            ->andWhere(['=', 'subject_cat_id', $model_subject->subject_cat_id])
            ->andWhere(['=', 'subject_id', $model_subject->subject_id])
            ->andWhere(['=', 'subject_vid_id', $model_subject->subject_vid_id]);
        if ($term_mastering != null) {
            $sect = $sect->andWhere(['=', 'term_mastering', $term_mastering]);
        }
        $sect = $sect->one();

        if (!$sect) {
            $this->stdout('Не найдена группа для программы: ' . $model_programm->id . " ", Console::FG_RED);
            $this->stdout("\n");
            return false;
        }
        $subject_sect_studyplan = SubjectSectStudyplan::find()->where(['=', 'subject_sect_id', $sect->id])
            ->andWhere(['=', 'plan_year', 2022])
            ->andWhere(['=', 'group_num', (int)$group_num]);
        if ($course != null) {
            $subject_sect_studyplan = $subject_sect_studyplan->andWhere(['=', 'course', $course]);
        }

        $subject_sect_studyplan = $subject_sect_studyplan->one() ?? new SubjectSectStudyplan();

        $subject_sect_studyplan->subject_sect_id = $sect->id;
        $subject_sect_studyplan->plan_year = 2022;
        $subject_sect_studyplan->group_num = $group_num;
        $subject_sect_studyplan->course = $course;
        $subject_sect_studyplan->subject_type_id = $this->getSubjectType($dd['type_training']);
        $subject_sect_studyplan->studyplan_subject_list = $this->getSubjectList($subject_sect_studyplan->studyplan_subject_list, $model_subject->id);

        $subject_sect_studyplan->save(false);
        return $subject_sect_studyplan->id;
    }

    private function getSubjectList($string, $item)
    {
        $arr = [];
        if ($string != '') {
            $arr = explode(',', $string);
        }
        array_push($arr, $item);

        return implode(',', array_unique($arr));
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
                        if ($ddd['week_time'] == 0) {
                            continue;
                        }
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
                            $model_subject->subject_vid_id = $this->getSubjectVid2($dd['vid_id']);
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

    public function generateGroup()
    {
        $models = \Yii::$app->db->createCommand('SELECT *
                                                    FROM generator_course_view 
                                                    ')->queryAll();

        if ($models) {
            foreach ($models as $item => $model) {
                $group = new SubjectSect();
                $group->programm_list = explode(',', $model['programm_list']);
                $group->course_list = explode(',', $model['course_list']);
                $group->term_mastering = $model['term_mastering'];
                $group->subject_cat_id = $model['subject_cat_id'];
                $group->subject_id = $model['subject_id'];
                $group->subject_vid_id = $model['subject_vid_id'];
                $group->subject_type_id = 1001;
                $group->sub_group_qty = $this->getGroupQty($model['term_mastering']);
                $group->sect_name = $model['sect_name'];
                $group->course_flag = $model['course_flag'];
                $group->save(false);
            }
            return true;
        }
        $this->stdout('Не загружены программы: ', Console::FG_RED);
        $this->stdout("\n");
        return false;
    }

    private function getGroupQty($qty)
    {

        switch ($qty) {
            case '1' :
                $sub_group_qty = 3;
                break;
            case '2' :
                $sub_group_qty = 6;
                break;
            case '3' :
            case '4' :
                $sub_group_qty = 5;
                break;
            case '5' :
                $sub_group_qty = 6;
                break;
            case '7' :
            case '8' :
                $sub_group_qty = 12;
                break;
            default :
                $sub_group_qty = 8;


        }
        return $sub_group_qty;
    }


    public function getSubjectType($name)
    {
        $position_id = 1001;
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
            case 'Композиция мультипликации' :
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
            case 'Классическая хореография' :
                $name = 'Классический танец';
                break;
            case 'ОФП' :
                $name = 'Общая физическая подготовка';
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
                'name' => $this->getSubjectName($name)
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
            576 => 1009, 578 => 1010, 570 => 1011, 573 => 1025, 557 => 1013, 591 => 1015, 598 => 1016, 589 => 1017, 592 => 1018,
            590 => 1019, 593 => 1020, 575 => 1021, 577 => 1022, 579 => 1023, 571 => 1024, 582 => 1026, 583 => 1027, 536 => 1050,
            580 => 1028, 581 => 1029, 556 => 1030, 559 => 1032, 537 => 1034, 542 => 1035, 540 => 1036, 534 => 1037, 552 => 1038,
            544 => 1039, 538 => 1040, 543 => 1041, 547 => 1042, 550 => 1043, 546 => 1044, 549 => 1045, 551 => 1046, 554 => 1047,
            555 => 1048, 563 => 1049, 539 => 1051, 545 => 1052, 558 => 1053, 565 => 1055, 572 => 1012, 535 => 1033, 597 => 1054,
            595 => 1056, 541 => 1057, 548 => 1058, 566 => 1059,
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

    public function findByTeachers2($full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT teachers_id 
                                                    FROM teachers_view 
                                                    WHERE fullname=:fullname 
                                                   ',
            [
                'fullname' => $this->lat2cyr($full_name)
            ])->queryOne();
        return $user['teachers_id'] ?? false;
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

    protected function setLessonProgress($studyplan_subject_id, $subject_sect_studyplan_id, $model_subject, $dd)
    {
        foreach ($dd['lessons'] as $item => $ddd) {
            // $transaction = \Yii::$app->db->beginTransaction();
            $flag = true;
            try {
                $model = LessonItems::find()->from('lesson_items')
                        ->where(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])
                        ->andWhere(['=', 'studyplan_subject_id', $studyplan_subject_id])
                        ->andWhere(['=', 'lesson_date', $ddd['lesson_date']])
                        ->one() ?? new LessonItems();

                $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
                $model->studyplan_subject_id = $studyplan_subject_id;
                $model->lesson_test_id = $this->getLessonTest($ddd['test_id']);
                $model->lesson_date = Yii::$app->formatter->asDate($ddd['lesson_date'], 'php:d.m.Y');
                $model->lesson_topic = $ddd['lesson_topic'];

                if (!$model->save(false)) {
                    //  $transaction->rollBack();
                    break;
                }
                foreach ($ddd['progress'] as $iii => $dddd) {
                    // print_r( $ddd);
                    $studyplanSubjectId = $this->findByStudyplanSubject($model_subject, $dddd['fio']);
                    if($studyplan_subject_id) {
                        $model_th = LessonProgress::find()
                                ->where(['=', 'lesson_items_id', $model->id])
                                ->andWhere(['=', 'studyplan_subject_id', $studyplanSubjectId])
                                ->one() ?? new LessonProgress();
                        $model_th->lesson_items_id = $model->id;
                        $model_th->studyplan_subject_id = $studyplanSubjectId;
                        $model_th->lesson_mark_id = $this->findMark($dddd['mark']);

                        if (!($model_th->save(false))) {
                            // $transaction->rollBack();
                            break;
                        }
                    }
                }

                if ($flag) {
                    // $transaction->commit();
//                    $this->stdout('Добавлен урок: ' . $model->id . " ", Console::FG_GREY);
//                    $this->stdout("\n");
                }
            } catch (\Exception $e) {
                // $transaction->rollBack();
                $this->stdout('Ошибка добавления урока: ', Console::FG_PURPLE);
                $this->stdout("\n");
            }
        }
        return true;
    }

    public function findByStudyplanSubject($model_subject, $full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT studyplan_subject.id as id
                                                    FROM studyplan_subject
                                                    JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
                                                    JOIN students_view ON students_view.students_id = studyplan.student_id
                                                    WHERE fullname=:fullname
                                                    AND studyplan_subject.studyplan_id=:studyplan_id 
                                                    AND studyplan_subject.subject_cat_id=:subject_cat_id 
                                                    AND studyplan_subject.subject_id=:subject_id 
                                                    AND studyplan_subject.subject_type_id=:subject_type_id 
                                                    ',
            [
                'fullname' => $this->lat2cyr($full_name),
                'studyplan_id' => $model_subject->studyplan_id ,
                'subject_cat_id' => $model_subject->subject_cat_id ,
                'subject_id' => $model_subject->subject_id ,
                'subject_type_id' => $model_subject->subject_type_id ,
            ])->queryOne();
        return $user['id'] ?? false;
    }


    private function findMark($mark)
    {
        $id = null;
        switch ($mark) {
            case 'ЗЧ' :
                $id = 1000;
                break;
            case 'НЗ' :
                $id = 1001;
                break;
            case 'НА' :
                $id = 1002;
                break;
            case '2' :
                $id = 1003;
                break;
            case '3-' :
                $id = 1004;
                break;
            case '3' :
                $id = 1005;
                break;
            case '3+' :
                $id = 1006;
                break;
            case '4-' :
                $id = 1007;
                break;
            case '4' :
                $id = 1008;
                break;
            case '4+' :
                $id = 1009;
                break;
            case '5-' :
                $id = 1010;
                break;
            case '5' :
                $id = 1011;
                break;
            case '5+' :
                $id = 1012;
                break;
            case 'Н' :
                $id = 1013;
                break;
            case 'П' :
                $id = 1014;
                break;
            case 'Б' :
                $id = 1015;
                break;
            case 'О' :
                $id = 1016;
                break;
            case '*' :
                $id = 1017;
                break;
        }
        return $id;
    }

    private function getLessonTest($m)
    {
        $id = 1000;
        switch ($m) {
            case 0 :
                $id = 1000;
                break;
            case 16 :
                $id = 1001;
                break;
            case 8 :
                $id = 1002;
                break;
            case 30 :
                $id = 1003;
                break;
            case 32 :
                $id = 1004;
                break;
            case 34 :
                $id = 1005;
                break;
            case 40 :
                $id = 1006;
                break;
            case 60 :
                $id = 1007;
                break;
            case 80 :
                $id = 1008;
                break;
            case  81:
            case  125:
            case  126:
                $id = 1009;
                break;
            case 127 :
                $id = 1010;
                break;

        }
        return $id;
    }

}