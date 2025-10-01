<?php

namespace common\models\schedule;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\Schedule;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "consult_schedule".
 *
 * @property int $id
 * @property int|null $teachers_load_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property int|null $auditory_id
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property TeachersLoad $teachersLoad
 */
class ConsultSchedule extends \yii\db\ActiveRecord
{
    use TeachersLoadTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['datetime_in', 'datetime_out'],
                'timeFormat' => 'd.m.Y H:i'
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['teachers_load_id', 'datetime_in', 'datetime_out', 'auditory_id'], 'required'],
            [['datetime_in', 'datetime_out', ], 'safe'],
            [['datetime_in', 'datetime_out'], 'checkFormatDateTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['datetime_out'], 'compareTimestamp', 'skipOnEmpty' => false],
            [['description'], 'string', 'max' => 512],
            [['teachers_load_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeachersLoad::className(), 'targetAttribute' => ['teachers_load_id' => 'id']],
        ];
    }

    public function checkFormatDateTime($attribute, $params)

    {
        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])(-|\.)(0[1-9]|1[0-2])(-|\.)[0-9]{4}(\s)([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/", $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода даты и времени не верен.');
        }
    }

    public function compareTimestamp($attribute, $params, $validator)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($this->datetime_out);

        if ($this->datetime_out && $timestamp_in >= $timestamp_out) {
            $message = 'Время окончания периода не может быть меньше или равно времени начала.';
            $this->addError($attribute, $message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'datetime_in' => Yii::t('art/guide', 'Time In'),
            'datetime_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art', 'Description'),
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
     * Gets query for [[TeachersLoad]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoad()
    {
        return $this->hasOne(TeachersLoad::className(), ['id' => 'teachers_load_id']);
    }

    public function getTeachersConsultNeed() {
        return true;
    }
    // Автоматическое добавление расписания для концертмейтера
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $model = TeachersLoad::find()
                    ->where(['=', 'id', $this->teachers_load_id])
                    ->andWhere(['=', 'direction_id', 1000])
                    ->one();
                if ($model) {
                    $modelFind = TeachersLoad::find()
                        ->where(['=', 'studyplan_subject_id', $model->studyplan_subject_id])
                        ->andWhere(['=', 'subject_sect_studyplan_id', $model->subject_sect_studyplan_id])
                        ->andWhere(['=', 'load_time', $model->load_time])
                        ->andWhere(['=', 'direction_id', 1001])
                        ->one();
                    if ($modelFind) {
                        $m = new ConsultSchedule();
                        $m->teachers_load_id = $modelFind->id;
                        $m->datetime_in = Yii::$app->formatter->asDatetime($this->datetime_in, 'php:d.m.Y H:i');
                        $m->datetime_out = Yii::$app->formatter->asDatetime($this->datetime_out, 'php:d.m.Y H:i');
                        $m->auditory_id = $this->auditory_id;
                        $m->save(false);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
