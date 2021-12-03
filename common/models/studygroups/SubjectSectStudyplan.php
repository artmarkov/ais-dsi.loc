<?php

namespace common\models\studygroups;

use artsoft\helpers\RefBook;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_sect_studyplan".
 *
 * @property int $id
 * @property int|null $subject_sect_id
 * @property string|null $studyplan_list
 * @property string $class_name
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SubjectSect $subjectSect
 */
class SubjectSectStudyplan extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_studyplan';
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
            [['subject_sect_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['class_name'], 'required'],
            [['studyplan_list'], 'string'],
            [['class_name'], 'string', 'max' => 64],
            [['class_name'], 'trim'],
            ['class_name', 'unique', 'targetAttribute' => ['class_name', 'subject_sect_id']],
            [['subject_sect_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSect::className(), 'targetAttribute' => ['subject_sect_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_list' => Yii::t('art/guide', 'Studyplan List'),
            'class_name' => Yii::t('art/guide', 'Class Name'),
            'created_at' => Yii::t('art/guide', 'Created'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[SubjectSect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSect()
    {
        return $this->hasOne(SubjectSect::className(), ['id' => 'subject_sect_id']);
    }

    /**
     * @return array
     */
    public function getStudyplan($readonly)
    {
        $data = [];
        if (!empty($this->studyplan_list)) {
            foreach (explode(',', $this->studyplan_list) as $item => $studyplan_subject_id) {
                $model = StudyplanSubject::findOne(['id' => $studyplan_subject_id]);
                $data[$studyplan_subject_id] = [
                    'content' => $this->getSubjectContent($studyplan_subject_id),
                    'disabled' => $readonly
                ];
            }
        }
        return $data;
    }

    /**
     * @param $studyplan_subject_id
     * @return string
     */
    public static function getSubjectContent($studyplan_subject_id)
    {
        $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
        return '<div class=""><div class="">' . RefBook::find('students_fio')->getValue($student_id) . '</div>'
            . '<div class="">' . RefBook::find('subject_memo_1')->getValue($studyplan_subject_id) . '</div></div>';
    }

    /**
     * удаляет злемент из studyplan_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function removeStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_list != '' ? $list = explode(',', $this->studyplan_list) : null;
        if (($key = array_search($studyplan_subject_id, $list)) !== false) {
            unset($list[$key]);
            $this->studyplan_list = implode(',', $list);
            $this->save(false);
        }
        return $this;
    }

    /**
     * добавляет злемент в studyplan_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function insertStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_list != '' ? $list = explode(',', $this->studyplan_list) : null;
        array_push($list, $studyplan_subject_id);
        $this->studyplan_list = implode(',', $list);
        $this->save(false);
        return $this;
    }
}
