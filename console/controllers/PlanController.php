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
 * run  console command:  php yii plan
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
                    $model->title = $v[10]; //Название мероприятия
                    $model->datetime_in = date('d.m.Y H:i', (integer)$v[1]); //Дата и время начала
                    $model->datetime_out = date('d.m.Y H:i', (integer)$v[2]); //Дата и время окончания
                    $model->places = $v[3] == 2 ? $v[4] : null; //Место проведения
                    $model->auditory_id = $v[3] == 1 ? $this->findByAuditoryNum($v[4]) : null; //Аудитория
                    $model->department_list = [$this->getDepartmentId($v[5])]; //Отделы
                    $model->executors_list = $this->getExecutors($v[34])[0]; //Ответственные
                    $model->category_id = $this->getCatId($v[46]); //Категория мероприятия

                    $model->form_partic = $v[24] == 0 ? 1 : 2; //Форма участия
                    $model->partic_price = $v[24] == 1 ? $v[25] : null; //Стоимость участия
                    $model->visit_poss = $v[26] == 0 ? 1 : 2; //Возможность посещения
                    $model->visit_content = $v[26] == 1 ? $v[27] : null; //Комментарий по посещению
                    $model->important_event = $v[32] == 0 ? 1 : 2; //Значимость мероприятия
                    $model->format_event = $v[33]; //Формат мероприятия
                    $model->region_partners = $v[17]; //Зарубежные и региональные партнеры
                    $model->site_url = $v[18]; //Ссылка на мероприятие (сайт/соцсети)
                    $model->site_media = $v[19]; //Ссылка на медиаресурс
                    $model->description = $v[11]; //Описание мероприятия
                    $model->rider = $v[12]; //Технические требования
                    $model->result = $v[13]; //Итоги мероприятия
                    $model->num_users = $v[14]; //Количество участников
                    $model->num_winners = $v[15]; //Количество победителей
                    $model->num_visitors = $v[16]; //Количество зрителей
                    $model->bars_flag = $v[30]; //Отправлено в БАРС
                    $model->period_over = $v[35]; //Период подготовки перед мероприятием мин.
                    $model->period_over_flag = $v[35] != '' ? 1 : 0; //
                    $model->executor_over_id = $this->findByTeachers($v[40]); //Ответственный за подготовку
                    $model->title_over = $v[42]; //Примечание

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
                                $file->name = $v[21] . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $filename[1];
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
                                $this->stdout('Не найден файл: ' . $filename[0] . " ", Console::FG_RED);
                                $this->stdout("\n");
                            }
                        }

                        if ($v[35] != '') {
                            $over = new ActivitiesOver();
                            $over->title = $v[42]; //Название мероприятия
                            $over->over_category = 2; //Категория мероприятия (подготовка, штатно, замена, отмена и пр.)
                            $over->datetime_in = date('d.m.Y H:i', (integer)$v[37]); //Дата и время начала
                            $over->datetime_out = date('d.m.Y H:i', (integer)$v[38]); //Дата и время окончания
                            $over->auditory_id = $v[43] != '' ? $this->findByAuditoryNum($v[43]) : null; //Аудитория
                            $over->department_list = [$this->getDepartmentId($v[39])];  //Отделы
                            $over->executors_list = [$this->findByTeachers($v[40])]; //Ответственные
                            $over->description = $v[45]; //Описание мероприятия'php:d.m.Y');
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
                                $efficArray = $this->getExecutors($v[34])[1];
                                $dateArray = $this->getExecutors($v[34])[2];
                                foreach ($efficArray as $id => $bonus) {

                                    if($bonus != null) {
                                        $m = new TeachersEfficiency();
                                        $m->teachers_id = $id;
                                        $m->efficiency_id = 1;
                                        $m->bonus_vid_id = $bonus < 500 ? 1 : 2;
                                        $m->bonus = $bonus;
                                        $m->date_in = isset($dateArray[$id]) ?  date('d.m.Y', (integer)$dateArray[$id]) : 0;
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
                } catch (Exception $e) {
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
        $ex = $pr = $dd = [];
        foreach (explode(',', $executors) as $item => $value) {
            $m = explode('||', $value);
            $t = $this->findByTeachers($m[0]);
            if ($t) {
                $ex[] = $t;
                $pr[$t] = $m[1] != 0 ? $m[1] : null;
                $dd[$t] = $m[2] != '' ? $m[2] : null;
            }
        }
        return [$ex, $pr, $dd];
    }

    public function findByTeachers($full_name)
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
        $ids = ['1' => '2', '2' => '3', '3' => '4', '4' => '5',
            '5' => '6', '6' => '7', '7' => '8', '8' => '10',
            '9' => '18', '10' => '11', '11' => '19', '12' => '12',
            '13' => '20', '14' => '13', '15' => '21', '17' => '26',
            '18' => '27', '20' => '33', '21' => '16', '22' => '29', '23' => '30',
            '24' => '31', '25' => '32', '26' => '14', '27' => '22',
            '28' => '15', '29' => '23', '30' => '24'];

        return $ids[$id] ?? '';
    }

}