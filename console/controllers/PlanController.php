<?php

namespace console\controllers;

use artsoft\fileinput\models\FileManager;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\activities\ActivitiesOver;
use common\models\efficiency\TeachersEfficiency;
use common\models\own\Department;
use common\models\schoolplan\Schoolplan;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run console command:  php yii plan
 *
 * @author markov-av
 */
class PlanController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("\n");
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
        $reader->open('data/plan.xlsx');
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
//                 print_r($v);
                $model = new Schoolplan();

                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    $model->title = $v[7]; //Название мероприятия
                    $model->datetime_in = date('d.m.Y H:i', (integer)$v[1]); //Дата и время начала
                    $model->datetime_out = date('d.m.Y H:i', (integer)$v[2]); //Дата и время окончания
                    $model->places = $v[3] == 2 ? $v[4] : null; //Место проведения
                    $model->auditory_id = $v[3] == 1 ? $this->findByAuditoryNum($v[4]) : null; //Аудитория
                    $model->department_list = [$this->getDepartmentId($v[5])]; //Отделы
                    $model->executors_list = $this->getExecutors($v[31]); //Ответственные
                    $model->category_id = $this->getCatId($v[6]); //Категория мероприятия

                    $model->form_partic = $v[21] == 0 ? 1 : 2; //Форма участия
                    $model->partic_price = $v[21] == 1 ? $v[22] : null; //Стоимость участия
                    $model->visit_poss = $v[23] == 0 ? 1 : 2; //Возможность посещения
                    $model->visit_content = $v[23] == 1 ? $v[24] : null; //Комментарий по посещению
                    $model->important_event = $v[29] == 0 ? 1 : 2; //Значимость мероприятия
                    $model->format_event = $v[30]; //Формат мероприятия
                    $model->region_partners = $v[14]; //Зарубежные и региональные партнеры
                    $model->site_url = $v[15]; //Ссылка на мероприятие (сайт/соцсети)
                    $model->site_media = $v[16]; //Ссылка на медиаресурс
                    $model->description = $v[8]; //Описание мероприятия
                    $model->rider = $v[9]; //Технические требования
                    $model->result = $v[10]; //Итоги мероприятия
                    $model->num_users = $v[11]; //Количество участников
                    $model->num_winners = $v[12]; //Количество победителей
                    $model->num_visitors = $v[13]; //Количество зрителей
                    $model->bars_flag = $v[27]; //Отправлено в БАРС
                    $model->period_over = $v[33]; //Период подготовки перед мероприятием мин.
                    $model->period_over_flag = $v[33] != '' ? 1 : 0; //
                    $model->executor_over_id = $this->findByTeachers2($v[38]); //Ответственный за подготовку
                    $model->title_over = $v[32] != '' ? $v[42] : ''; //Примечание
                    $model->author_id = $this->findByTeachers($v[38]); //Автор записи

                    if ($flag = $model->save(false)) {
                        $patch = "frontend/web/uploads/fileinput/schoolplan/";
                        $filenames = [
                            ["frontend/web/uploads/afisha/big/" . $v[0] . ".pdf", "pdf"],
                            ["frontend/web/uploads/afisha/big/" . $v[0] . ".jpg", "jpg"],
                            ["frontend/web/uploads/program/" . $v[0] . ".pdf", "pdf"],
                            ["frontend/web/uploads/program/" . $v[0] . ".jpg", "jpg"],
                        ];
                        foreach ($filenames as $item => $filename) {
                            if (file_exists($filename[0])) {
                                $file = new FileManager();
                                $file->orig_name = $v[0] . '.' . $filename[1];
                                $file->name = $v[18] . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $filename[1];
                                $file->size = filesize($filename[0]);
                                $file->type = ArrayHelper::getValue(FileManager::TYPE, $filename[1] . '.type') ? ArrayHelper::getValue(FileManager::TYPE, $filename[1] . '.type') : 'image';
                                $file->filetype = ArrayHelper::getValue(FileManager::TYPE, $filename[1] . '.filetype');
                                $file->item_id = $model->id;
                                $file->class = 'Schoolplan';
                                $file->sort = 0;

                                $filename_new = $patch . $file->name;
                                copy($filename[0], $filename_new);
                                $file->save(false);
                            } else {
//                                $this->stdout('Не найден файл: ' . $filename[0] . " ", Console::FG_RED);
//                                $this->stdout("\n");
                            }
                        }

                        if ($v[35] != '') {
                            $over = new ActivitiesOver();
                            $over->title = $v[42]; //Название мероприятия
                            $over->over_category = 2; //Категория мероприятия (подготовка, штатно, замена, отмена и пр.)
                            $over->datetime_in = date('d.m.Y H:i', (integer)$v[35]); //Дата и время начала
                            $over->datetime_out = date('d.m.Y H:i', (integer)$v[36]); //Дата и время окончания
                            $over->auditory_id = $v[41] != '' ? $this->findByAuditoryNum($v[41]) : null; //Аудитория
                            $over->department_list = [$this->getDepartmentId($v[37])];  //Отделы
                            $over->executors_list = [$this->findByTeachers2($v[38])]; //Ответственные
                            $over->description = $v[43]; //Описание мероприятия'php:d.m.Y');
                            if (!($flag = $over->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                            if ($flag) {
                                $m = Schoolplan::findOne(['id' => $model->id]);
                                $m->activities_over_id = $over->id; //ИД мероприятия вне плана (подготовка к мероприятию)
                                if (!($flag = $m->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                            if ($flag) {
                                $efficArray = $this->getExecutors2($v[31])[0];
                                $dateArray = $this->getExecutors2($v[31])[1];
                                foreach ($efficArray as $id => $bonus) {

                                    if ($bonus != 0) {
                                        $m = new TeachersEfficiency();
                                        $m->teachers_id = $id;
                                        $m->efficiency_id = 1;
                                        $m->bonus_vid_id = $bonus < 500 ? 1 : 2;
                                        $m->bonus = $bonus;
                                        $m->date_in = isset($dateArray[$id]) ? date('d.m.Y', (integer)$dateArray[$id]) : 0;
                                        $m->class = 'Schoolplan';
                                        $m->item_id = $model->id;
                                        if (!($flag = $m->save())) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->stdout('Добавлено мероприятие: ' . $model->id, Console::FG_GREY);
                        $this->stdout("\n");
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
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

    public function getExecutors($executors)
    {
        $ex = [];
        foreach (explode(',', $executors) as $item => $value) {
            $m = explode('||', $value);
            $t = $this->findByTeachers2($m[0]);
            if ($t) {
                $ex[] = $t;
            }
        }
        return $ex;
    }

    public function getExecutors2($executors)
    {
        $pr = $dd = [];
        foreach (explode(',', $executors) as $item => $value) {
            $m = explode('||', $value);
            $t = $this->findByTeachers2($m[0]);
            if ($t) {
                $pr[$t] = $m[1];
                $dd[$t] = $m[2] != '' ? $m[2] : null;
            }
        }
        return [$pr, $dd];
    }

    public function findByTeachers($full_name)
    {
        $user = \Yii::$app->db->createCommand('SELECT user_common_id 
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

    public function getDepartmentId($name)
    {
        $model = Department::findOne(['name' => $name]);
        return $model ? $model->id : false;
    }

    public function getCatId($id)
    {
        $ids = [1 => 2, 2 => 3, 3 => 4, 4 => 5,
            5 => 6, 6 => 7, 7 => 8, 8 => 11,
            9 => 18, 10 => 12, 11 => 20, 12 => 13,
            13 => 21, 14 => 14, 15 => 22, 17 => 27,
            18 => 28, 20 => 9, 21 => 17, 22 => 30, 23 => 31,
            24 => 32, 25 => 33, 26 => 15, 27 => 23,
            28 => 16, 29 => 24, 30 => 25];

        return $ids[$id] ?? null;
    }

}