<?php

namespace common\models\service;

use common\models\user\UserCommon;
use Yii;

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
    public $time_rezerv = 600; // время на открытие аудитории

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
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'date' => 'Дата',
            'timestamp_work_in' => 'Время прихода на работу',
            'timestamp_work_out' => 'Время ухода с работы',
            'timestamp_activities_in' => 'Время начала работы по расписанию',
            'timestamp_activities_out' => 'Время окончания работы по расписанию',
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

}
