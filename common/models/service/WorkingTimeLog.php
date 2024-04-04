<?php

namespace common\models\service;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\ExcelObjectList;
use common\models\efficiency\EfficiencyTree;
use common\models\user\UserCommon;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "working_time_log".
 *
 * @property int $id
 * @property int $user_common_id
 * @property string|null $date
 * @property int|null $timestamp_work_in Время прихода на работу
 * @property int|null $timestamp_work_out Время ухода с работы
 * @property int|null $timestamp_activities_in Время начала работы по расписанию
 * @property int|null $timestamp_activities_out Время окончания работы по расписанию
 * @property string|null $comment
 *
 * @property UserCommon $userCommon
 */
class WorkingTimeLog extends \artsoft\db\ActiveRecord
{
    const TIME_RESERV = 600; // время на открытие аудитории
    const TIME_EXIT = 900; // время на закрытие аудитории

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'working_time_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id'], 'required'],
            [['user_common_id', 'timestamp_work_in', 'timestamp_work_out', 'timestamp_activities_in', 'timestamp_activities_out'], 'default', 'value' => null],
            [['user_common_id', 'timestamp_work_in', 'timestamp_work_out', 'timestamp_activities_in', 'timestamp_activities_out'], 'integer'],
            [['date'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::className(), 'targetAttribute' => ['user_common_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_common_id' => "Фамилия Имя Отчество",
            'date' => 'Дата',
            'timestamp_work_in' => 'Время прихода',
            'timestamp_work_out' => 'Время ухода',
            'timestamp_activities_in' => 'Время начала работы',
            'timestamp_activities_out' => 'Время окончания работы',
            'comment' => 'Комментарий',
        ];
    }

    /**
     * Gets query for [[UserCommon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCommon()
    {
        return $this->hasOne(UserCommon::className(), ['id' => 'user_common_id']);
    }

    /**
     * @param $model_date
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getSummaryData($model_date)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['id' => 'ID'];
        $attributes += ['name' => 'Фамилия И.О.'];
        $attributes += ['total_in' => 'Суммарное время опоздания на работу'];
        $attributes += ['total_out' => 'Суммарное время уходов с работы раньше положенного'];
        $attributes += ['total_truancy' => 'Колличество прогулов за период'];
        $attributes += ['total_reserv' => 'Число приходов на работу менее чем за ' . self::TIME_RESERV/60 . ' минут'];
        $attributes += ['total_exit' => 'Число уходов с работы поэже ' . self::TIME_EXIT/60 . ' минут после окончания работы'];

        $models = self::find()
            ->where(['between', 'date', Yii::$app->formatter->asDate($timestamp_in, 'php:Y-m-d'), Yii::$app->formatter->asDate($timestamp_out, 'php:Y-m-d')])
            ->asArray()->all();
        $usersIds = ArrayHelper::getColumn($models, 'user_common_id');
        $teachers = (new Query())->from('teachers_view')
            ->where(['user_common_id' => $usersIds])
            ->all();
        $teachersName = ArrayHelper::map($teachers, 'user_common_id', 'fio');
        $res = [];
        foreach ($models as $model) {
            if (!$model['timestamp_work_in'] && !$model['timestamp_work_out'])  {
                $res[$model['user_common_id']]['total_truancy'] = isset($res[$model['user_common_id']]['total_truancy']) ? $res[$model['user_common_id']]['total_truancy'] + 1 : 1;
            } elseif ($model['timestamp_work_in'] > $model['timestamp_activities_in']) {
                $res[$model['user_common_id']]['total_in'] = isset($res[$model['user_common_id']]['total_in']) ? $res[$model['user_common_id']]['total_in'] + ($model['timestamp_work_in'] - $model['timestamp_activities_in']) : ($model['timestamp_work_in'] - $model['timestamp_activities_in']);
            } elseif (($model['timestamp_activities_in'] - $model['timestamp_work_in']) < self::TIME_RESERV) {
                $res[$model['user_common_id']]['total_reserv'] = isset($res[$model['user_common_id']]['total_reserv']) ? $res[$model['user_common_id']]['total_reserv'] + 1 : 1;
            } elseif ($model['timestamp_work_out'] < $model['timestamp_activities_out']) {
                $res[$model['user_common_id']]['total_out'] = isset($res[$model['user_common_id']]['total_out']) ? $res[$model['user_common_id']]['total_out'] + ($model['timestamp_activities_out'] - $model['timestamp_work_out']) : ($model['timestamp_activities_out'] - $model['timestamp_work_out']);
            } elseif (($model['timestamp_work_out'] - $model['timestamp_activities_out']) > self::TIME_EXIT) {
                $res[$model['user_common_id']]['total_exit'] = isset($res[$model['user_common_id']]['total_exit']) ? $res[$model['user_common_id']]['total_exit'] + 1 : 1;
            }
        }
        $data = [];
        foreach ($teachersName as $id => $name) {
                $data[$id]['id'] = $id;
                $data[$id]['name'] = $name;
                $data[$id]['total_in'] = isset($res[$id]['total_in']) ? $res[$id]['total_in']/60 : 0;
                $data[$id]['total_out'] = isset($res[$id]['total_out']) ? $res[$id]['total_out']/60 : 0;
                $data[$id]['total_truancy'] = isset($res[$id]['total_truancy']) ? $res[$id]['total_truancy'] : 0;
                $data[$id]['total_reserv'] = isset($res[$id]['total_reserv']) ? $res[$id]['total_reserv'] : 0;
                $data[$id]['total_exit'] = isset($res[$id]['total_exit']) ? $res[$id]['total_exit'] : 0;
        }
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
       /* usort($data, function ($a, $b) {
            return $b['total_in'] <=> $a['total_in'];
        });*/

        return ['data' => $data, 'attributes' => $attributes];
    }
    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_warking-time-log.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }

}
