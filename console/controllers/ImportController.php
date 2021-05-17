<?php

namespace console\controllers;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use artsoft\models\User;
use common\models\employees\Employees;
use common\models\guidejob\Bonus;
use common\models\own\Department;
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
 * run  console command:  php yii import/employees
 *
 * @author markov-av
 */
class ImportController extends Controller
{
    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/teachers.xlsx');
        $this->stdout("\n");
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
                    $user->username = $v[15] ? $v[15] : $this->generateUsername($v[1], $v[2], $v[3]);
                    $user->password = $v[16];
                    $user->email = $v[17];
                    $user->email_confirmed = $v[17] ? 1 : 0;
                    $user->generateAuthKey();
                    $user->status = User::STATUS_ACTIVE;
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'teacher']);

                        $userCommon->user_id = $user->id;
                        $userCommon->user_category = UserCommon::USER_CATEGORY_TEACHERS;
                        $userCommon->status = UserCommon::STATUS_ACTIVE;
                        $userCommon->last_name = $v[1];
                        $userCommon->first_name = $v[2];
                        $userCommon->middle_name = $v[3];
                        $userCommon->gender = $v[4] == 'М' ? 1 : 2;
                        $userCommon->birth_date = \Yii::$app->formatter->asDate($v[5], 'php:d.m.Y');
                        $userCommon->phone = str_replace('-', ' ', $v[6]);
                        $userCommon->phone_optional = str_replace('-', ' ', $v[7]);
                        if ($flag = $userCommon->save(false)) {
                            $model->user_common_id = $userCommon->id;
                            $model->position_id = $this->getPositionId($v[10]);
                            $model->level_id = $this->getLevelId($v[11]);
                            $model->tab_num = $v[13];
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
                                if ($v[20] != '') {
                                    $activity = new TeachersActivity();
                                    $activity->teachers_id = $model->id;
                                    $activity->work_id = $v[14] == 'Основная' ? 1 : 2;
                                    $activity->direction_id = $v[20] == 'Педагогическая' ? 1 : 2;
                                    $activity->stake_id = $this->getStakeId($v[21]);
                                    if (!($flag = $activity->save(false))) {
                                         $transaction->rollBack();
                                        break;
                                    }
                                }
                                if ($v[22] != '') {
                                    $activity = new TeachersActivity();
                                    $activity->teachers_id = $model->id;
                                    $activity->work_id = $v[14] == 'Основная' ? 1 : 2;
                                    $activity->direction_id = $v[22] == 'Педагогическая' ? 1 : 2;
                                    $activity->stake_id = $this->getStakeId($v[23]);

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
        $this->stdout("\n");
    }

    public function actionEmployees()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/employees.xlsx');
        $this->stdout("\n");
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
                    $user->username = $v[9] ? $v[9] : $this->generateUsername($v[1], $v[2], $v[3]);
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
                        $userCommon->gender = $v[4] == 'М' ? 1 : 2;
                        $userCommon->birth_date = \Yii::$app->formatter->asDate($v[5], 'php:d.m.Y');
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
        $this->stdout("\n");
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
                $stake_id = 1;
                break;
            case 'СК' :
                $stake_id = 2;
                break;
            case 'ПК' :
                $stake_id = 3;
                break;
            case 'ВК' :
                $stake_id = 4;
                break;
        }
        return $stake_id;
    }

    public function getPositionId($name)
    {
        $position_id = null;

        switch ($name) {
            case 'Директор' :
                $position_id = 1;
                break;
            case 'Заместители директора' :
                $position_id = 2;
                break;
            case 'Руководители отделов' :
                $position_id = 3;
                break;
            case 'Преподаватели' :
                $position_id = 4;
                break;
        }
        return $position_id;
    }

    public function getLevelId($name)
    {
        $level_id = null;

        switch ($name) {
            case 'Высшее образование' :
                $level_id = 1;
                break;
            case 'Высшее непроф' :
                $mevel_id = 2;
                break;
            case 'Неполное высшее' :
                $level_id = 3;
                break;
            case 'Среднее проф' :
                $level_id = 4;
                break;
        }
        return $level_id;
    }

    /**
     * @param $last_name
     * @param $first_name
     * @param $middle_name
     * @return string
     */
    public function generateUsername($last_name, $first_name, $middle_name)
    {
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
}
