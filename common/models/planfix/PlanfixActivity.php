<?php

namespace common\models\planfix;

use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\db\ActiveRecord;

/**
 * This is the model class for table "planfix_activity".
 *
 * @property int $id
 * @property int $planfix_id
 * @property int $planfix_activity_category Категория этапа работы(Отчет по работе, Дополнительное задание, Доработка, Уточнение задания, Приемка работы)
 * @property string|null $executor_comment Комментарий исполнителя
 * @property string|null $author_comment Комментарий автора работы
 * @property int $activity_status Статус этапа работы(В работе, Принято, Отклонено)
 * @property string|null $activity_status_reason Причина отклонения этапа работы
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Planfix $planfix
 * @property User $createdBy
 * @property User $updatedBy
 */
class PlanfixActivity extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'planfix_activity';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['planfix_activity_category'], 'required'],
            [['planfix_id', 'planfix_activity_category', 'activity_status'], 'default', 'value' => null],
            [['planfix_id', 'planfix_activity_category', 'activity_status', 'version'], 'integer'],
            [['executor_comment', 'author_comment'], 'string', 'max' => 512],
            [['activity_status_reason'], 'string', 'max' => 1024],
           // [['planfix_id'], 'exist', 'skipOnError' => true, 'targetClass' => Planfix::className(), 'targetAttribute' => ['planfix_id' => 'id']],
           // [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
           // [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'planfix_id' => 'Planfix ID',
            'planfix_activity_category' => 'Категория этапа работы',
            'executor_comment' => 'Комментарий исполнителя',
            'author_comment' => 'Комментарий автора работы',
            'activity_status' => 'Статус этапа работы',
            'activity_status_reason' => 'Причина отклонения этапа работы',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Planfix]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlanfix()
    {
        return $this->hasOne(Planfix::className(), ['id' => 'planfix_id']);
    }

    public static function getActivityCategoryList()
    {
        return [
            1 => 'Отчет',
            2 => 'Дополнительное задание',
            3 => 'Доработка',
            4 => 'Уточнение задания',
            5 => 'Приемка работы',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusActivityList()
    {
        return [
            1 => 'В работе',
            2 => 'Принято',
            3 => 'Отклонено',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusActivityOptionsList()
    {
        return [
            [1, 'В работе', 'info'],
            [2, 'Принято', 'success'],
            [3, 'Отклонено', 'danger']
        ];
    }

}
