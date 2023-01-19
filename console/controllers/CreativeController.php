<?php

namespace console\controllers;

use artsoft\fileinput\models\FileManager;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\creative\CreativeWorks;
use common\models\efficiency\TeachersEfficiency;
use common\models\own\Department;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii creative
 *
 * @author markov-av
 */
class CreativeController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("\n");
        $this->addCreative();

    }

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function addCreative()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open('data/creative.xlsx');
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
                $model = new CreativeWorks();

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $teachers_id = $this->findByTeachers($v[1], $v[2], $v[3]);
                    $model->category_id = $this->getCatId($v[4]);
                    $model->name = $v[5];
                    $model->description = $v[6];
                    $model->department_list = [$this->getDepartmentId($v[7])];
                    $model->teachers_list = [$teachers_id];
                    $model->status = $v[9] == 1 ? 0 : 1;
                    if (is_a($v[8], 'DateTime')) { // если объект DateTime
                        $v[8] = $v[8]->format('d-m-Y');
                    }
                    $model->published_at = \Yii::$app->formatter->asDate($this->getDate($v[8]), 'php:d.m.Y');

                    if ($flag = $model->save(false)) {
                        $filename = "frontend/web/uploads/creative/" . $v[0] . ".pdf";
                        if (file_exists($filename)) {
                            $file = new FileManager();
                            $file->orig_name = $v[0] . ".pdf";
                            $file->name = strtotime($model->published_at) . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . "pdf";
                            $file->size = filesize($filename);
                            $file->type = 'pdf';
                            $file->item_id = $model->id;
                            $file->class = 'CreativeWorks';
                            $file->sort = 0;

                            $filename_new = "frontend/web/uploads/fileinput/creativeworks/" . $file->name;
                            copy($filename, $filename_new);
                            $file->save(false);
                        } else {
                            $this->stdout('Не найден файл: ' . $filename . " ", Console::FG_RED);
                            $this->stdout("\n");
                        }

                        for ($i = 10; $i <= 27; $i = $i + 2) {
                            if ($v[$i] && $teachers_id) {
                                $effic = new TeachersEfficiency();
                                $effic->class = 'CreativeWorks';
                                $effic->item_id = $model->id;
                                $effic->efficiency_id = $this->getEfficienceId($v[$i + 1]);
                                $effic->teachers_id = $teachers_id;
                                $effic->bonus = $v[$i + 1];
                                $effic->bonus_vid_id =  $v[$i + 1] < 500 ? 1 : 2;
                                if (is_a($v[$i], 'DateTime')) { // если объект DateTime
                                    $v[$i] = $v[$i]->format('d-m-Y');
                                }
                                $effic->date_in = \Yii::$app->formatter->asDate($this->getDate($v[$i]), 'php:d.m.Y');
                                if (!($flag = $effic->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->stdout('Добавлена работа: ' . $model->id, Console::FG_GREY);
                        $this->stdout("\n");
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
    }

    public function findByTeachers($last_name, $first_name, $middle_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT teachers_id 
                                                    FROM teachers_view 
                                                    WHERE last_name=:last_name 
                                                    AND first_name=:first_name 
                                                    AND middle_name=:middle_name',
            [
                'last_name' => $this->lat2cyr($last_name),
                'first_name' => $this->lat2cyr($first_name),
                'middle_name' => $this->lat2cyr($middle_name)
            ])->queryOne();
        return $user['teachers_id'];
    }

    /**
     * @param $name
     * @return bool|int
     */
    public function getEfficienceId($name)
    {
        $stake_id = null;

        switch ($name) {
            case '3' :
            case '4' :
            case '5' :
            case '6' :
                $stake_id = 4;
                break;
            case '7' :
            case '8' :
            case '9' :
            case '10' :
            case '11' :
                $stake_id = 5;
                break;
            case '12' :
            case '13' :
            case '14' :
            case '15' :
                $stake_id = 6;
                break;
            default:
                $stake_id = 5;
        }
        return $stake_id;
    }

    public function getDepartmentId($name)
    {
        $model = Department::findOne(['name' => $name]);
        return $model ? $model->id : false;
    }

    public function getCatId($name)
    {
        $stake_id = null;

        switch ($name) {
            case 'Творческие работы' :
                $stake_id = 1000;
                break;
            case 'Методические работы' :
                $stake_id = 1001;
                break;
            case 'Сертификаты' :
                $stake_id = 1002;
                break;
        }
        return $stake_id;
    }

    protected function getDate($date)
    {
        if (is_a($date, 'DateTime')) { // если объект DateTime
            $date = $date->format('d-m-Y');
            return $date;
        }
        return $date;
    }

    protected function lat2cyr($text) {
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
            'x' =>'х'
        );
        return strtr($text, $arr);
    }

}
