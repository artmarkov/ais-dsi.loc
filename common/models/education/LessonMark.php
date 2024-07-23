<?php

namespace common\models\education;

use artsoft\models\User;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_lesson_mark".
 *
 * @property int $id
 * @property int $mark_category
 * @property string $mark_label
 * @property string $mark_hint
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
    const PRESENCE = 0;
    const MARK = 1;
    const OFFSET_NONOFFSET = 2;
    const REASON_ABSENCE = 3;
    const MARK_10 = 4;

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
//            [['mark_value', 'mark_label'], 'unique', 'when' => function ($model) {
//                return $model->isNewRecord;
//            }, 'enableClientValidation' => false],
            [['mark_value'], 'number'],
            [['mark_value'], 'required', 'when' => function ($model) {
                return $model->mark_category == self::MARK;
            }, 'enableClientValidation' => false],
            [['mark_category', 'status', 'sort_order'], 'integer'],
            [['mark_label'], 'string', 'max' => 8],
            [['mark_hint'], 'string', 'max' => 64],
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
            'mark_hint' => Yii::t('art/guide', 'Mark Hint'),
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
            self::PRESENCE => 'Факт присутствия',
            self::MARK => 'Оценка 5-ти бальная',
            self::MARK_10 => 'Оценка 10-ти бальная',
            self::OFFSET_NONOFFSET => 'Зачет/Незачет',
            self::REASON_ABSENCE => 'Причины отсутствия',
        );
    }

    public static function getMarkCatogoryValue($val)
    {
        $ar = self::getMarkCatogoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public static function getMarkHints()
    {
        return ArrayHelper::map(self::find()
            ->select('mark_label, mark_hint')
            ->where(['!=', 'mark_hint', ''])
            ->asArray()->all(), 'mark_label', 'mark_hint');
    }

    public static function getMarkLabelForEntrant($mark_category = self::MARK)
    {
        return ArrayHelper::map(self::find()
            ->select('id, mark_label')
            ->where(['is not', 'mark_value', null])
            ->andWhere(['=', 'mark_category', $mark_category])
            ->orderBy('mark_value')
            ->asArray()->all(), 'id', 'mark_label');
    }

    public static function getMarkLabelForStudent($mark_category = [self::MARK, self::PRESENCE])
    {
        return ArrayHelper::map(self::find()
            ->select('id, mark_label')
            ->where(['mark_category' => $mark_category])
            ->orderBy('mark_label')
            ->asArray()->all(), 'id', 'mark_label');
    }
}
