<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use artsoft\models\User;
use common\models\employees\Employees;
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
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii import
 *
 * @author markov-av
 */
class ImportController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("\n");

//        $this->addEmployees();
//        $this->stdout("\n");

        $this->addTeachers();
        $this->stdout("\n");

        $this->addStudents();
        $this->stdout("\n");

        $this->addParents();
    }

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function addTeachers()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/teachers.xlsx');
        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue; // skip header
                }
//                if ($i > 2) {
//                    continue; // skip header
//                }
                /* @var $row Row */
                $v = $row->toArray();
                $user = new User();
                $userCommon = new UserCommon();
                $model = new Teachers();

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user->username = $this->generateUsername($v[15], $v[1], $v[2], $v[3]);
                    $user->password = $v[16];
                    $user->email = $v[17];
                    $user->email_confirmed = $v[17] ? 1 : 0;
                    $user->generateAuthKey();
                    $user->status = $v[20] == 0 ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'teacher']);

                        $userCommon->user_id = $user->id;
                        $userCommon->user_category = UserCommon::USER_CATEGORY_TEACHERS;
                        $userCommon->status = $v[20] == 0 ? UserCommon::STATUS_ACTIVE : UserCommon::STATUS_ARCHIVE;
                        $userCommon->last_name = $v[1];
                        $userCommon->first_name = $v[2];
                        $userCommon->middle_name = $v[3];
                        $userCommon->gender = $this->getGender($v[4]);
                        $userCommon->birth_date = \Yii::$app->formatter->asDate($this->getDate($v[5]), 'php:d.m.Y');
                        $userCommon->phone = str_replace('-', ' ', $v[6]);
                        $userCommon->phone_optional = str_replace('-', ' ', $v[7]);
                        if ($flag = $userCommon->save(false)) {
                            $model->user_common_id = $userCommon->id;
                            $model->position_id = $this->getPositionId($v[10]);
                            $model->level_id = $this->getLevelId($v[11]);
                            $model->tab_num = $v[13];
                            $model->work_id = $v[14] == 'Основная' ? 1000 : 1001;
                            $dep = [];
                            foreach (explode(';', $v[19]) as $name) {
                                if ($name != '') {
                                    $id = $this->getDepartmentId($name);
                                    if ($id) {
                                        $dep[] = $id;
                                    }
                                }
                            }
                            $model->department_list = $dep;
                            $bonus = [];
                            foreach (explode(';', $v[12]) as $name) {
                                if ($name != '') {
                                    $id = $this->getBonusId($name);
                                    if ($id) {
                                        $bonus[] = $id;
                                        $model->bonus_summ += Bonus::findOne(['id' => $id])->value_default;
                                    }
                                }
                            }
                            $model->bonus_list = $bonus;
                            if ($flag = $model->save(false)) {
                                if ($v[21] != '') {
                                    $activity = new TeachersActivity();
                                    $activity->teachers_id = $model->id;
                                    $activity->direction_vid_id = 1000;
                                    $activity->direction_id = $v[21] == 'Педагогическая' ? 1000 : 1001;
                                    $activity->stake_id = $this->getStakeId($v[22]);
                                    if (!($flag = $activity->save(false))) {
                                         $transaction->rollBack();
                                        break;
                                    }
                                }
                                if ($v[23] != '') {
                                    $activity = new TeachersActivity();
                                    $activity->teachers_id = $model->id;
                                    $activity->direction_vid_id = 1001;
                                    $activity->direction_id = $v[23] == 'Педагогическая' ? 1000 : 1001;
                                    $activity->stake_id = $this->getStakeId($v[24]);

                                    if (!($flag = $activity->save(false))) {
                                         $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($flag) {
                    $transaction->commit();
                        $this->stdout('Добавлен преподаватель: ' . $userCommon->last_name . ' ' . $userCommon->first_name . ' ' . $userCommon->middle_name . " ", Console::FG_GREY);
                        $this->stdout("\n");
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
    }

    public function addEmployees()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/employees.xlsx');

        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue; // skip header
                }
//                if ($i < 4) {
//                    continue; // skip header
//                }
                /* @var $row Row */
                $v = $row->toArray();
//                print_r($v);
                $user = new User();
                $userCommon = new UserCommon();
                $model = new Employees();

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user->username = $this->generateUsername($v[9], $v[1], $v[2], $v[3]);
                    $user->password = $v[10];
                    $user->email = $v[11];
                    $user->email_confirmed = $v[11] ? 1 : 0;
                    $user->generateAuthKey();
                    $user->status = User::STATUS_ACTIVE;
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'employees']);

                        $userCommon->user_id = $user->id;
                        $userCommon->user_category = UserCommon::USER_CATEGORY_EMPLOYEES;
                        $userCommon->status = UserCommon::STATUS_ACTIVE;
                        $userCommon->last_name = $v[1];
                        $userCommon->first_name = $v[2];
                        $userCommon->middle_name = $v[3];
                        $userCommon->gender = $this->getGender($v[4]);
                        $userCommon->birth_date = \Yii::$app->formatter->asDate($this->getDate($v[5]), 'php:d.m.Y');
                        $userCommon->phone = str_replace('-', ' ', $v[6]);
                        $userCommon->phone_optional = str_replace('-', ' ', $v[7]);
                        $userCommon->address = $v[8];
                        if ($flag = $userCommon->save(false)) {
                            $model->user_common_id = $userCommon->id;
                            $model->position = $v[13];
                            if (!($flag = $model->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->stdout('Добавлен сотрудник: ' . $userCommon->last_name . ' ' . $userCommon->first_name . ' ' . $userCommon->middle_name . " ", Console::FG_GREY);
                        $this->stdout("\n");
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
    }

    public function addStudents()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/students.xlsx');
        foreach ($reader->getSheetIterator() as $k => $sheet) {
            if (1 != $k) {
                continue;
            }
            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue; // skip header
                }
//                if ($i < 5) {
//                    continue; // skip header
//                }
                /* @var $row Row */
                $v = $row->toArray();
               // print_r($v);
                $user = new User();
                $userCommon = new UserCommon();
                $model = new Student();

                if($v[18] == 'Абитуриенты' || $v[18] == 'Ученики школы') {
                $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $user->username = $this->generateUsername($v[19], $v[1], $v[2], $v[3]);
                        $user->password = $v[20];
                        $user->email = $v[21];
                        $user->email_confirmed = $v[21] ? 1 : 0;
                        $user->generateAuthKey();
                        $user->status = ($v[18] == 'Абитуриенты' || $v[18] == 'Ученики школы') ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
                        if ($flag = $user->save(false)) {
                            $user->assignRoles(['user', 'student']);

                            $userCommon->user_id = $user->id;
                            $userCommon->user_category = UserCommon::USER_CATEGORY_STUDENTS;
                            $userCommon->status = ($v[18] == 'Абитуриенты' || $v[18] == 'Ученики школы') ? UserCommon::STATUS_ACTIVE : UserCommon::STATUS_ARCHIVE;
                            $userCommon->last_name = $v[1];
                            $userCommon->first_name = $v[2];
                            $userCommon->middle_name = $v[3];
                            $userCommon->gender = $this->getGender($v[4]);
                            if (is_a($v[5], 'DateTime')) { // если объект DateTime
                                $v[5] = $v[5]->format('d-m-Y');
                            }
                            $userCommon->birth_date = \Yii::$app->formatter->asDate($this->getDate($v[5]), 'php:d.m.Y');
                            $userCommon->phone = str_replace('-', ' ', $v[6]);
                            $userCommon->phone_optional = str_replace('-', ' ', $v[7]);
                            $userCommon->address = $v[8];
                            $userCommon->snils = str_replace('-', '.', $v[15]);
                            if ($flag = $userCommon->save(false)) {
                                $model->user_common_id = $userCommon->id;
                                $model->position_id = $this->getPosId($v[18]);
                                $model->sert_name = $v[10] ? 'birth_cert' : '';
                                $model->sert_series = $v[11];
                                $model->sert_num = $v[12];
                                $model->sert_organ = $v[13];
                                if($v[14]) {
                                    if (is_a($v[14], 'DateTime')) { // если объект DateTime
                                        $v[14] = $v[14]->format('d-m-Y');
                                    }
                                    $model->sert_date = \Yii::$app->formatter->asDate($this->getDate($v[14]), 'php:d.m.Y');
                                }
                                if (!($flag = $model->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $this->stdout('Добавлен ученик: ' . $userCommon->last_name . ' ' . $userCommon->first_name . ' ' . $userCommon->middle_name . " ", Console::FG_GREY);
                            $this->stdout("\n");
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }
    }

    public function addParents()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/parents.xlsx');
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
//                 print_r($v);
                $user = new User();
                $userCommon = new UserCommon();
                $model = new Parents();

                $student_id = $this->findByStudent($v[5], $v[6], $v[7]);
                $parent_id = $this->findByParent($v[1], $v[2], $v[3]);
                if(!$parent_id) {
                    if ($student_id) {
                        $transaction = \Yii::$app->db->beginTransaction();
                        try {

                            $user->username = $this->generateUsername($v[15], $v[1], $v[2], $v[3]);
                            $user->password = $v[16];
                            $user->email = $v[17];
                            $user->email_confirmed = $v[17] ? 1 : 0;
                            $user->generateAuthKey();
                            $user->status = $v[15] ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
                            if ($flag = $user->save(false)) {
                                $user->assignRoles(['user', 'curator']);

                                $userCommon->user_id = $user->id;
                                $userCommon->user_category = UserCommon::USER_CATEGORY_PARENTS;
                                $userCommon->status = UserCommon::STATUS_ACTIVE;
                                $userCommon->last_name = trim($v[1]);
                                $userCommon->first_name = trim($v[2]);
                                $userCommon->middle_name = trim($v[3]);
                                $userCommon->gender = $this->getGender($v[8]);
                                if (is_a($v[9], 'DateTime')) { // если объект DateTime
                                    $v[9] = $v[9]->format('d-m-Y');
                                }
                                $userCommon->birth_date = \Yii::$app->formatter->asDate($this->getDate($v[9]), 'php:d.m.Y');
                                $userCommon->phone = str_replace('-', ' ', $v[12]);
                                $userCommon->phone_optional = $v[10] ? str_replace('-', ' ', $v[10]) : str_replace('-', ' ', $v[11]);
                                $userCommon->snils = str_replace('-', '.', $v[14]);
                                if ($flag = $userCommon->save(false)) {
                                    $model->user_common_id = $userCommon->id;

                                    if ($flag = $model->save(false)) {

                                        if ($student_id) {
                                            $studentDependence = new StudentDependence();
                                            $studentDependence->student_id = $student_id;
                                            $studentDependence->parent_id = $model->id;
                                            $studentDependence->relation_id = $this->getRelationId($v[4]);

                                            $flag = $studentDependence->save(false);
                                        } else {
                                            $this->stdout('Не найдена запись: ' . $v[5] . ' ' . $v[6] . ' ' . $v[7] . " ", Console::FG_RED);

                                        }
                                    }
                                }
                            }
                            if ($flag) {
                                $transaction->commit();
                                $this->stdout('Добавлен родитель: ' . $userCommon->last_name . ' ' . $userCommon->first_name . ' ' . $userCommon->middle_name . " ", Console::FG_GREY);
                                $this->stdout("\n");
                            }
                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
                else {
                    if ($student_id) {
                        $studentDependence = new StudentDependence();
                        $studentDependence->student_id = $student_id;
                        $studentDependence->parent_id = $parent_id;
                        $studentDependence->relation_id = $this->getRelationId($v[4]);

                        $studentDependence->save(false);
                        $this->stdout('Добавлена связь для: ' . $v[5] . ' ' . $v[6] . ' ' . $v[7] . " ", Console::FG_GREEN);
                        $this->stdout("\n");
                    } else {
                        $this->stdout('Не найдена запись: ' . $v[5] . ' ' . $v[6] . ' ' . $v[7] . " ", Console::FG_RED);
                        $this->stdout("\n");

                    }
                }

            }
        }
    }
    /**
     * @param $name
     * @return bool|int
     */
    public function getBonusId($name)
    {
        $model = Bonus::findOne(['name' => $name]);
        return $model ? $model->id : false;
    }

    public function getDepartmentId($name)
    {
        $model = Department::findOne(['name' => $name]);
        return $model ? $model->id : false;
    }

    public function getStakeId($name)
    {
        $stake_id = null;

        switch ($name) {
            case 'БК' :
                $stake_id = 1000;
                break;
            case 'СК' :
                $stake_id = 1001;
                break;
            case 'ПК' :
                $stake_id = 1002;
                break;
            case 'ВК' :
                $stake_id = 1003;
                break;
        }
        return $stake_id;
    }

    public function getPositionId($name)
    {
        $position_id = null;

        switch ($name) {
            case 'Директор' :
                $position_id = 1000;
                break;
            case 'Заместители директора' :
                $position_id = 1001;
                break;
            case 'Руководители отделов' :
                $position_id = 1002;
                break;
            case 'Преподаватели' :
                $position_id = 1003;
                break;
        }
        return $position_id;
    }

    public function getLevelId($name)
    {
        $level_id = null;

        switch ($name) {
            case 'Высшее образование' :
                $level_id = 1000;
                break;
            case 'Высшее непроф' :
                $level_id = 1001;
                break;
            case 'Неполное высшее' :
                $level_id = 1002;
                break;
            case 'Среднее проф' :
                $level_id = 1003;
                break;
        }
        return $level_id;
    }

    public function getPosId($name)
    {
        $position_id = null;

        switch ($name) {
            case 'Абитуриенты' :
                $position_id = 1000;
                break;
            case 'Ученики школы' :
                $position_id = 1001;
                break;
            case 'Выпускники школы' :
                $position_id = 1002;
                break;
            case 'Отчислены из школы' :
                $position_id = 1003;
                break;
            case 'Не прошли испытания' :
                $position_id = 1004;
                break;
        }
        return $position_id;
    }
    /**
     * @param $last_name
     * @param $first_name
     * @param $middle_name
     * @return string
     */
    public function generateUsername($name, $last_name, $first_name, $middle_name)
    {
       if(!User::findByUsername($name)){
           return $name;
       }
       $i = 0;
        $last_name = $this->slug($last_name);
        $first_name = $this->slug($first_name);
        $middle_name = $this->slug($middle_name);
        do {
            $username = $last_name . '-' . substr($first_name, 0, ++$i) . substr($middle_name, 0, 1);
        } while (User::findByUsername($username));

        return $username;
    }

    protected static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = preg_replace('/[^\p{L}\p{Nd}]+/u', $replacement, $string);
        $string = TransliteratorHelper::process($string, 'UTF-8');
        return $lowercase ? mb_strtolower($string) : $string;
    }

    protected function getDate($date){
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
        return $user['students_id'];
    }

    public function findByParent($last_name, $first_name, $middle_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT parents_id 
                                                    FROM parents_view 
                                                    WHERE last_name=:last_name 
                                                    AND first_name=:first_name 
                                                    AND middle_name=:middle_name',
            [
                'last_name' => $last_name,
                'first_name' => $first_name,
                'middle_name' => $middle_name
            ])->queryOne();
        return $user['parents_id'];
    }

    public function getRelationId($name)
    {
        $stake_id = null;

        switch ($name) {
            case 'мать' :
                $stake_id = 1;
                break;
            case 'отец' :
                $stake_id = 2;
                break;
            case 'бабушка' :
                $stake_id = 3;
                break;
            case 'дедушка' :
                $stake_id = 4;
                break;
            case 'брат' :
                $stake_id = 5;
                break;
            case 'сестра' :
                $stake_id = 6;
                break;
            case 'опекун' :
                $stake_id = 7;
                break;
            default :
                $stake_id = 8;
        }
        return $stake_id;
    }

    public function getGender($name){
        return $name == 'М' ? 1 : ($name == 'Ж' ? 2 : 0);
    }
}
