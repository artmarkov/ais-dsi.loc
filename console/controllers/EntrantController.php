<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use artsoft\models\User;
use common\models\employees\Employees;
use common\models\entrant\Entrant;
use common\models\entrant\EntrantGroup;
use common\models\guidejob\Bonus;
use common\models\own\Department;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\students\StudentDependence;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\user\UserCommon;
use dosamigos\transliterator\TransliteratorHelper;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii entrant
 *
 * @author markov-av
 */
class EntrantController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("\n");
        $this->addEntrant();
        $this->stdout("\n");

    }


    public function addEntrant()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/entrant_2023.xlsx');
        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue; // skip header
                }
//                if ($i > 5) {
//                    continue; // skip header
//                }
                /* @var $row Row */
                $v = $row->toArray();
//                print_r($v);
                $student_id = $this->findByStudent($this->lat2cyr(trim($v[0])), $this->lat2cyr(trim($v[1])), $this->lat2cyr(trim($v[2])));

                if ($student_id) {
                    $subjects = $this->getSubjects($v[4]);
                    $comm_id = $this->getComm($v[6]);
                    $group_id = $this->getGroup($v, $comm_id);
                    $model = \Yii::$app->db->createCommand('SELECT id 
                                                    FROM entrant 
                                                    WHERE student_id= :student_id 
                                                    AND comm_id= :comm_id',
                        [
                            'student_id' => $student_id,
                            'comm_id' => $comm_id,
                        ])->queryOne();
                    if (!$model) {
                        $model = new Entrant();
                        $model->student_id = $student_id;
                        $model->comm_id = $comm_id;
                        $model->group_id = $group_id;
                        $model->subject_list = $subjects;
                        $model->last_experience = $v[5];
                        $model->remark = '';
                        $model->status = 0;
                        $model->decision_id = 0;

                        if ($model->save(false)) {
                            $this->stdout('Добавлен абитуриент: ' . $v[0] . ' ' . $v[1] . ' ' . $v[2] . " ", Console::FG_GREY);
                            $this->stdout("\n");
                        }
                    }
                }
            }
        }
    }

    public function getComm($id)
    {
        $position_id = null;
        switch ($id) {
            case '31' :
                $position_id = 1000;
                break;
            case '32' :
                $position_id = 1001;
                break;
        }
        return $position_id;
    }

    public function getGroup($v, $comm_id)
    {

        $model = \Yii::$app->db->createCommand('SELECT id 
                                                    FROM entrant_group 
                                                    WHERE name= :name 
                                                    AND timestamp_in= :timestamp_in 
                                                    AND comm_id= :comm_id',
            [
                'name' => $v[8],
                'timestamp_in' => $v[9],
                'comm_id' => $comm_id,
            ])->queryOne();

        if (!$model['id']) {
            $model = new EntrantGroup();
            $model->name = $v[8];
            $model->timestamp_in = \Yii::$app->formatter->asDatetime($v[9], 'php:d.m.Y H:i');
            $model->comm_id = $comm_id;
            $model->prep_flag = 0;
            if ($model->save(false)) {
                $this->stdout('Добавлена группа: ' . $v[8] . " ", Console::FG_BLUE);
                $this->stdout("\n");
            }

        }
        return $model ? $model['id'] : false;
    }

    public function getSubjects($line)
    {
        $subject = [];
        $array = explode(',', $line);
        foreach ($array as $name) {
            $subject[] = $this->getSubject($name);
        }
        return $subject;
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

    protected function getDate($date)
    {
        if (is_a($date, 'DateTime')) { // если объект DateTime
            $date = $date->format('d-m-Y');
            return $date;
        }
        return $date;
    }

    public function findByStudent($last_name, $first_name, $middle_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT students_id 
                                                    FROM students_view 
                                                    WHERE last_name=:last_name 
                                                    AND first_name=:first_name 
                                                    AND middle_name=:middle_name',
            [
                'last_name' => $last_name,
                'first_name' => $first_name,
                'middle_name' => $middle_name
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
