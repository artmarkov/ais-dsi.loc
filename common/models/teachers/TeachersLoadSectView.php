<?php

namespace common\models\teachers;

use Yii;

class TeachersLoadSectView extends TeachersLoadView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load_sect_view';
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'teachers_load_week_time' => Yii::t('art/guide', 'Week Time'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'subject_schedule_id' => Yii::t('art/guide', 'Subject Schedule'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'course' => Yii::t('art/studyplan', 'Course'),
        ];
    }
}
