<?php

namespace common\models\education;

use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attestation_items".
 *
 * @property int $id
 * @property int|null $plan_year
 * @property int $studyplan_subject_id Учебный предмет ученика
 * @property int|null $lesson_mark_id Оценка
 * @property string|null $mark_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property LessonMark $lessonMark
 * @property StudyplanSubject $studyplanSubject
 */
class AttestationItems extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attestation_items';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_year', 'studyplan_subject_id', 'lesson_mark_id', 'version'], 'integer'],
            [['plan_year', 'studyplan_subject_id'], 'required'],
            [['plan_year', 'studyplan_subject_id'], 'unique', 'targetAttribute' => ['plan_year', 'studyplan_subject_id']],
            [['mark_rem'], 'string', 'max' => 127],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'studyplan_subject_id' => Yii::t('art/student', 'Student'),
            'lesson_mark_id' => Yii::t('art/guide', 'Mark'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
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
     * Gets query for [[LessonMark]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonMark()
    {
        return $this->hasOne(LessonMark::className(), ['id' => 'lesson_mark_id']);
    }

    /**
     * Gets query for [[StudyplanSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasOne(StudyplanSubject::className(), ['id' => 'studyplan_subject_id']);
    }

    /**
     * Инициация аттестац оценок для инд. занятий create/update
     * @param $teachers_id
     * @param $subject_key
     * @return array
     */
    public static function getAttestationsForTeachers($teachers_id, $subject_key)
    {
        $modelsItems = [];
        $keyArray = explode('||', $subject_key);
        $subject_key = $keyArray[0];
        $plan_year = $keyArray[1];
        $active = LessonProgressView::find()
            ->where(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
            ->andWhere(['med_cert' => true])
            ->all();
        $studyplanSubjectIds = ArrayHelper::getColumn($active, 'studyplan_subject_id');
        $models = self::find()
            ->where(['plan_year' => $plan_year])
            ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
            ->all();
        $models = ArrayHelper::index($models, 'studyplan_subject_id');
        foreach ($active as $item => $dataItem) {
            if (isset($models[$dataItem['studyplan_subject_id']])) {
                $m = self::find()
                        ->where(['plan_year' => $plan_year])
                        ->andWhere(['studyplan_subject_id' => $dataItem['studyplan_subject_id']])
                        ->one() ?? new self();
                $m->studyplan_subject_id = $dataItem['studyplan_subject_id'];
                $m->plan_year = $dataItem['plan_year'];
                $modelsItems[] = $m;
            } else {
                $m = new self;
                $m->studyplan_subject_id = $dataItem['studyplan_subject_id'];
                $m->plan_year = $dataItem['plan_year'];
                $modelsItems[] = $m;
            }
        }
        return $modelsItems;
    }

    public static function getAttestationsGroupForTeachers($teachers_id, $subject_key)
    {
        $modelsItems = [];
        $keyArray = explode('||', $subject_key);
        $subject_sect_studyplan_id = $keyArray[0];
        $plan_year = $keyArray[1];
        $active = LessonProgressView::find()
            ->where(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
            ->andWhere(['med_cert' => true])
            ->all();
        $studyplanSubjectIds = ArrayHelper::getColumn($active, 'studyplan_subject_id');
        $models = self::find()
            ->where(['plan_year' => $plan_year])
            ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
            ->all();
        $models = ArrayHelper::index($models, 'studyplan_subject_id');
        foreach ($active as $item => $dataItem) {
            if (isset($models[$dataItem['studyplan_subject_id']])) {
                $m = self::find()
                        ->where(['plan_year' => $plan_year])
                        ->andWhere(['studyplan_subject_id' => $dataItem['studyplan_subject_id']])
                        ->one() ?? new self();
                $m->studyplan_subject_id = $dataItem['studyplan_subject_id'];
                $m->plan_year = $dataItem['plan_year'];
                $modelsItems[] = $m;
            } else {
                $m = new self;
                $m->studyplan_subject_id = $dataItem['studyplan_subject_id'];
                $m->plan_year = $dataItem['plan_year'];
                $modelsItems[] = $m;
            }
        }
        return $modelsItems;
    }

    public static function getAttestationsForStudyplan($subject_key)
    {
        $model = [];
        $keyArray = explode('||', $subject_key);
        $studyplan_subject_id = $keyArray[0];
        $plan_year = $keyArray[1];

        if (self::isAttestationNeeds($studyplan_subject_id, $plan_year)) {
            $model = self::find()
                    ->where(['plan_year' => $plan_year])
                    ->andWhere(['studyplan_subject_id' => $studyplan_subject_id])
                    ->one() ?? new self();
            $model->studyplan_subject_id = $studyplan_subject_id;
            $model->plan_year = $plan_year;
        }

        return $model;
    }

    public static function isAttestationNeeds($studyplan_subject_id, $plan_year)
    {
        return LessonProgressView::find()
            ->where(['=', 'studyplan_subject_id', $studyplan_subject_id])
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(['med_cert' => true])
            ->exists();
    }
}
