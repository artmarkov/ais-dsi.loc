<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_lesson_test".
 *
 * @property int $id
 * @property string $division_list
 * @property int|null $test_category
 * @property string $test_name
 * @property string|null $test_name_short
 * @property int $plan_flag
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 */
class LessonTest extends \artsoft\db\ActiveRecord
{
    const CURRENT_WORK = 1;
    const MIDDLE_ATTESTATION = 2;
    const FINISH_ATTESTATION = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_lesson_test';
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
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['division_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_list', 'test_name', 'test_name_short', 'test_category'], 'required'],
            [['test_category', 'plan_flag', 'status', 'sort_order'], 'integer'],
            [['division_list'], 'safe'],
            [['test_name'], 'string', 'max' => 64],
            [['test_name_short'], 'string', 'max' => 16],
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
            'division_list' => Yii::t('art/guide', 'Division List'),
            'test_category' => Yii::t('art/guide', 'Test Category'),
            'test_name' => Yii::t('art/guide', 'Test Name'),
            'test_name_short' => Yii::t('art/guide', 'Test Name Short'),
            'plan_flag' => Yii::t('art/guide', 'Plan Flag'),
            'status' => Yii::t('art', 'Status'),
            'sort_order' => Yii::t('art/guide', 'Order'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * getTestCatogoryList
     * @return array
     */
    public static function getTestCatogoryList()
    {
        return array(
            self::CURRENT_WORK => Yii::t('art/guide', 'Current Work'),
            self::MIDDLE_ATTESTATION => Yii::t('art/guide', 'Middle Attestation'),
            self::FINISH_ATTESTATION => Yii::t('art/guide', 'Finish Attestation'),
        );
    }

    /**
     * getTestCatogoryValue
     * @param string $val
     * @return string
     */
    public static function getTestCatogoryValue($val)
    {
        $ar = self::getTestCatogoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getLessonTestList($model, $category = [1,2,3], $division_list = false)
    {
        if(is_array($division_list)) {
            $division_list = implode(',',$division_list);
        }
        $query = self::find()->select(new \yii\db\Expression('
        id, test_name, test_category,
            CASE
                WHEN test_category = ' . self::CURRENT_WORK . ' THEN \'' . Yii::t('art/guide', 'Current Work') . '\'
                WHEN test_category = ' . self::MIDDLE_ATTESTATION . ' THEN \'' . Yii::t('art/guide', 'Middle Attestation') . '\'
                WHEN test_category = ' . self::FINISH_ATTESTATION . ' THEN \'' . Yii::t('art/guide', 'Finish Attestation') . '\'
            END test_category_name
            '))->where(['test_category' => $category]);

        if ($model->isNewRecord) {
            $query = $query->andWhere(['=', 'status', self::STATUS_ACTIVE]);
        }
        if ($division_list) {
            $query = $query->andWhere(new Expression("string_to_array(division_list, ','::text)::text[] && string_to_array('{$division_list}', ','::text)::text[]")); // сравнение массивов
        }
        return \yii\helpers\ArrayHelper::map($query->orderBy('test_category, sort_order')->asArray()->all(), 'id', 'test_name', 'test_category_name');
    }

    /**
     * @return array
     */
    public static function getLessonTestHints()
    {
        return ArrayHelper::map(self::find()
            ->select('test_name_short, test_name')
            ->asArray()->all(), 'test_name_short', 'test_name');
    }

    public function getLessonTestCategory($id)
    {
        $lessonTest = self::findOne($id);
        return $lessonTest ? $lessonTest->test_category : false;
    }
}
