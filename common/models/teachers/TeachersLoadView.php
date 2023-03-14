<?php

namespace common\models\teachers;

use Yii;
use yii\helpers\ArrayHelper;

class TeachersLoadView extends TeachersLoad
{

    public static function tableName()
    {
        return 'teachers_load_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'subject_sect_id' => Yii::t('art/guide', 'Sect Name'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'status' => Yii::t('art/guide', 'Status'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'load_time' => Yii::t('art/guide', 'Load Time'),
            'load_time_consult' => Yii::t('art/guide', 'Load Time Consult'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'subject' => Yii::t('art/guide', 'Subject'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_type_name' => Yii::t('art/guide', 'Subject Type Name'),
        ];
    }

    public static function getSectListForTeachers($teachers_id, $plan_year)
    {
        $list = self::find()
            ->select('subject_id, subject_name')
            ->distinct()
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(['teachers_id' => $teachers_id])
            ->orderBy('subject_name')
            ->all();

        return ArrayHelper::map($list, 'subject_id',  'subject_name');
    }

//    public function getStudyplanWeekTime()
//    {
//        $funcSql = <<< SQL
//    select MAX(week_time)
//	from studyplan_subject
//	where id = any(string_to_array('{$this->studyplan_subject_list}', ',')::int[])
//SQL;
//
//        return $this->studyplan_subject_list ? \Yii::$app->db->createCommand($funcSql)->queryScalar() : 0;
//    }

//    /**
//     * Проверка на необходимость добавления нагрузки
//     * @return bool
//     */
//    public function getTeachersLoadsNeed()
//    {
//        return $this->getTeachersFullLoad() < $this->week_time;
//    }


}
