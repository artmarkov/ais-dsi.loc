<?php

namespace common\models\education;

use artsoft\models\User;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "guide_lesson_mark".
 *
 * @property int $id
 * @property int $mark_category
 * @property string $mark_label
 * @property float|null $mark_value
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 */
class LessonMark extends \artsoft\db\ActiveRecord
{
    const MARK = 1;
    const PASS = 2;
    const FAILED = 3;
    const NOT_SERTIFIED = 4;
    const ABSENCE_DISRES = 5;
    const ABSENCE_GOOD = 6;
    const ABSENCE_ILLNESS = 7;
    const LATE = 8;
    const ATTEND = 9;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_lesson_mark';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            'grid-sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mark_label', 'mark_category'], 'required'],
            [['mark_category'], 'unique', 'when' => function ($model) {
                return $model->mark_category != self::MARK;
            }, 'enableClientValidation' => false],
            [['mark_value'], 'number'],
            [['mark_value'], 'required', 'when' => function ($model) {
                return $model->mark_category == self::MARK;
            }, 'enableClientValidation' => false],
            [['mark_category', 'status', 'sort_order'], 'integer'],
            [['mark_label'], 'string', 'max' => 8],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'mark_label' => Yii::t('art/guide', 'Mark Label'),
            'mark_category' => Yii::t('art/guide', 'Mark Category'),
            'mark_value' => Yii::t('art/guide', 'Mark Value'),
            'status' => Yii::t('art', 'Status'),
            'sort_order' => Yii::t('art/guide', 'Order'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * getTestCatogoryList
     * @return array
     */
    public static function getMarkCatogoryList()
    {
        return array(
            self::MARK => 'Оценка',
            self::PASS => 'Зачет',
            self::FAILED => 'Незачет',
            self::NOT_SERTIFIED => 'Не аттестован',
            self::ABSENCE_DISRES => 'Отсутствие по неуважительной причине',
            self::ABSENCE_GOOD => 'Отсутствие по уважительной причине',
            self::ABSENCE_ILLNESS => 'Отсутствие по причине болезни',
            self::LATE => 'Опоздание на урок',
            self::ATTEND => 'Присутствие на занятии',
        );
    }

    public static function getMarkCatogoryValue($val)
    {
        $ar = self::getMarkCatogoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
