<?php

namespace common\models\efficiency;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use Yii;
use common\models\teachers\Teachers;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teachers_efficiency".
 *
 * @property int $id
 * @property int $efficiency_id
 * @property int $teachers_id
 * @property int $item_id
 * @property string|null $bonus
 * @property int $date_in
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property string $class
 *
 * @property GuideEfficiencyTree $efficiency
 * @property Teachers $teachers
 */
class TeachersEfficiency extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_efficiency';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['date_in'],
                'timeFormat' => 'd.m.Y',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['efficiency_id', 'teachers_id', 'date_in', 'bonus'], 'required'],
            [['efficiency_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'item_id'], 'integer'],
            [['date_in', 'teachers_id'], 'safe'],
            [['version'], 'default', 'value' => 0],
            [['bonus'], 'string', 'max' => 127],
            ['class', 'string'],
            [['efficiency_id'], 'exist', 'skipOnError' => true, 'targetClass' => EfficiencyTree::class, 'targetAttribute' => ['efficiency_id' => 'id']],
           // [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'efficiency_id' => Yii::t('art/guide', 'Efficiency'),
            'class' => Yii::t('art/guide', 'Class'),
            'item_id' => Yii::t('art/guide', 'Item'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'bonus' => Yii::t('art/guide', 'Bonus'),
            'date_in' => Yii::t('art/guide', 'Date Bonus In'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Efficiency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEfficiency()
    {
        return $this->hasOne(EfficiencyTree::class, ['id' => 'efficiency_id']);
    }


    /* Геттер для названия категории */
    public function getEfficiencyName()
    {
        return $this->efficiency->name;
    }

    public function getTeachersName()
    {
        return $this->teachers->getFullName();
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @param $model_date
     * @return array
     */
    public static function getSummaryData($model_date)
    {

        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $models = self::find()
            ->where(['between', 'date_in', $timestamp_in, $timestamp_out])
            ->asArray()->all();

        $tree = EfficiencyTree::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();

        $res = [];
        $all_summ = 0;
        foreach ($models as $model) {
            $res[$model['teachers_id']][$tree[$model['efficiency_id']]] = isset($res[$model['teachers_id']][$tree[$model['efficiency_id']]]) ? $res[$model['teachers_id']][$tree[$model['efficiency_id']]] + $model['bonus'] : $model['bonus'];
            $res[$model['teachers_id']]['total'] = isset($res[$model['teachers_id']]['total']) ? $res[$model['teachers_id']]['total'] + $model['bonus'] : $model['bonus'];
        }
        $data = [];
        foreach (\artsoft\helpers\RefBook::find('teachers_fio', \common\models\user\UserCommon::STATUS_ACTIVE)->getList() as $id => $name) {
            $data[$id] = $res[$id] ?? ['total' => null];
            $data[$id]['id'] = $id;
            $data[$id]['name'] = $name;
            $data[$id]['stake'] = \artsoft\helpers\RefBook::find('teachers_stake')->getValue($id);
            $data[$id]['total_sum'] = $data[$id]['total'] * $data[$id]['stake'] * 0.01;
            $all_summ += $data[$id]['total_sum'];
            $data[$id]['date_in'] = $timestamp_in;
            $data[$id]['date_out'] = $timestamp_out;
        }
        return ['data' => $data, 'all_summ' => $all_summ];
    }
}
