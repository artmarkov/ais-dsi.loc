<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\ExcelObjectList;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Mosticket extends Model
{
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
//            ['file', 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Файл с данными пропусков портала Мосбилет',
        ];
    }

    public function getData()
    {
        $dataMosbilet = [];
        $data = [];
        $attributes = [
            'fullname' => 'ФИО ученика',
            'birth_date' => 'Дата рождения',
            'status_ticket' => 'Статус проверки'
        ];
        $dataAis = self::getStudentList();

        $fileName = Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $this->file->baseName . '.' . $this->file->extension;
        if ($this->validate()) {
            $this->file->saveAs($fileName);
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($fileName);
            foreach ($reader->getSheetIterator() as $k => $sheet) {
                if (1 != $k) {
                    continue;
                }
                foreach ($sheet->getRowIterator() as $i => $row) {
                    if (1 == $i) {
                        continue;
                    }
                    /* @var $row Row */
                    $v = $row->toArray();
                    if (is_a($v[2], 'DateTime')) { // если объект DateTime
                        $v[2] = $v[2]->format('d.m.Y');
                    }
                    $dataMosbilet[] = [
                        'fullname' => trim($v[0]),
                        'birth_date' => Yii::$app->formatter->asTimestamp(trim($v[2])),
                    ];
                }
            }
            $student_list = ArrayHelper::index($dataAis, null, ['fullname', 'birth_date']);
//            echo '<pre>' . print_r($student_list, true) . '</pre>'; die();
            foreach ($dataMosbilet as $index => $val) {
                $status_ticket = 'Не найден в АИС';
                if (isset($student_list[$val['fullname']][$val['birth_date']])) {
                    $status_ticket = 'Найден в АИС и Мосбилет';
                }

                $data[] = [
                    'fullname' => $val['fullname'],
                    'birth_date' => Yii::$app->formatter->asDate($val['birth_date']),
                    'status_ticket' => $status_ticket,
                ];
            }
            $mosbilet_list = ArrayHelper::index($dataMosbilet, null, ['fullname', 'birth_date']);
            foreach ($dataAis as $index => $val) {
                if (!isset($mosbilet_list[$val['fullname']][$val['birth_date']])) {
                    $data[] = [
                        'fullname' => $val['fullname'],
                        'birth_date' => Yii::$app->formatter->asDate($val['birth_date']),
                        'status_ticket' => 'Не найден в Мосбилет',
                    ];
                }

            }
            usort($data, function ($a, $b) {
            return $a['fullname'] <=> $b['fullname'];
        });


        }
        return ['data' => $data, 'attributes' => $attributes];
    }
    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }
//            $x->addData(['stake' => 'Итого', 'total' => $data['all_summ']]);

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_mosticket.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }

    protected static function getStudentList()
    {
        return (new Query())->from('studyplan_stat_view')
            ->select(new \yii\db\Expression('TRIM(TRAILING \' \' FROM student_fio ) as fullname, student_birth_date as birth_date'))
            ->distinct()
            ->where(['status' => 1])
            ->andWhere(['plan_year' => ArtHelper::getStudyYearDefault()])
            ->all();
    }
}