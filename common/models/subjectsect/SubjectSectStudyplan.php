<?php

namespace common\models\subjectsect;

use artsoft\helpers\RefBook;
use common\models\subject\SubjectType;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject_sect_studyplan".
 *
 * @property int $id
 * @property int|null $subject_sect_id
 * @property string|null $studyplan_subject_list
 * @property string $class_name
 * @property int $subject_type_id
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
            [['subject_sect_id', 'subject_type_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['class_name', 'subject_type_id'], 'required'],
            [['studyplan_subject_list'], 'string'],
            [['class_name'], 'string', 'max' => 64],
            [['class_name'], 'trim'],
            ['class_name', 'unique', 'targetAttribute' => ['class_name', 'subject_sect_id'], 'message' => 'Назавание группы не должно повторяться.'],
            [['subject_sect_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSect::class, 'targetAttribute' => ['subject_sect_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'class_name' => Yii::t('art/guide', 'Class Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
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
     * Gets query for [[SubjectSect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSect()
    {
        return $this->hasOne(SubjectSect::class, ['id' => 'subject_sect_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoads()
    {
        return $this->hasMany(TeachersLoad::class, ['subject_sect_studyplan_id' => 'id']);
    }

    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'subject_type_id']);
    }
    /**
     * @return array
     */
    public function getSubjectSectTeachersLoads()
    {
        $data = [];
        foreach ($this->teachersLoads as $item => $modelTeachersLoad) {
            $data[$modelTeachersLoad->id] = RefBook::find('teachers_load_display')->getValue($modelTeachersLoad->id);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getSubjectSectStudyplans($readonly = false)
    {
        $data = [];
        if (!empty($this->studyplan_subject_list)) {
            foreach (explode(',', $this->studyplan_subject_list) as $item => $studyplan_subject_id) {
                $data[$studyplan_subject_id] = [
                    'content' => $this->getSubjectSectStudyplanContent($studyplan_subject_id),
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
    public static function getSubjectSectStudyplanContent($studyplan_subject_id)
    {
        $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
        return '<div style="overflow: hidden;">
                <div class="pull-left">' . RefBook::find('students_fio')->getValue($student_id) . '</div>' .
               '<div class="fa-pull-right">' . RefBook::find('subject_memo_1')->getValue($studyplan_subject_id) . '</div></div>';
    }

    /**
     * удаляет злемент из studyplan_subject_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function removeStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_subject_list != '' ? $list = explode(',', $this->studyplan_subject_list) : null;
        if (($key = array_search($studyplan_subject_id, $list)) !== false) {
            unset($list[$key]);
            $this->studyplan_subject_list = implode(',', $list);
            $this->save(false);
        }
        return $this;
    }

    /**
     * добавляет злемент в studyplan_subject_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function insertStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_subject_list != '' ? $list = explode(',', $this->studyplan_subject_list) : null;
        array_push($list, $studyplan_subject_id);
        $this->studyplan_subject_list = implode(',', $list);
        $this->save(false);
        return $this;
    }
}
