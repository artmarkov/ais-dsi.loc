<?php

namespace common\models\teachers;

use artsoft\helpers\RefBook;
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
            'direction_vid_id' => Yii::t('art/teachers', 'Name Direction Vid'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'load_time' => Yii::t('art/guide', 'Load Time'),
            'load_time_0' => 'Факт бюд.',
            'load_time_1' => 'Факт в/б.',
            'load_time_consult' => Yii::t('art/guide', 'Load Time Consult'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'subject' => Yii::t('art/guide', 'Subject'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_type_name' => Yii::t('art/guide', 'Subject Type Name'),
            'programm_id' => 'Программа',
            'education_programm_short_name' => 'Программа',
        ];
    }

    public static function getTotal($provider, $fieldName, $teachers_id)
    {
        $total = [0, 0];
//        echo '<pre>' . print_r($provider, true) . '</pre>'; die();
        foreach ($provider as $item) {
            if ($item['teachers_id'] == $teachers_id) {
                if ($item['direction_id'] == 1000) {
                    $total[0] += $item[$fieldName];
                } else {
                    $total[1] += $item[$fieldName];
                }
            }
        }

        return $total[0] . '/' . $total[1];
    }


    public static function getSubjectListForTeachers($teachers_id, $plan_year)
    {
        $q = Yii::$app->db->createCommand('SELECT distinct subject as id, subject as name
	FROM teachers_load_view where teachers_id IS NOT NULL AND teachers_id=:teachers_id AND plan_year=:plan_year',
            ['teachers_id' => $teachers_id,
                'plan_year' => $plan_year
            ])->queryAll();
        $data = ArrayHelper::map($q, 'id', 'name');

        return $data;

    }

    public static function getProgrammListForTeachers($teachers_id, $plan_year)
    {
        $q = Yii::$app->db->createCommand('SELECT distinct programm_id as id, education_programm_short_name as name
	FROM teachers_load_view where teachers_id IS NOT NULL AND programm_id IS NOT NULL AND teachers_id=:teachers_id AND plan_year=:plan_year',
            ['teachers_id' => $teachers_id,
                'plan_year' => $plan_year
            ])->queryAll();
        $data = ArrayHelper::map($q, 'id', 'name');

        return $data;

    }

    public static function getStudyplanTotal($provider, $fieldName)
    {
        $total = 0;
        foreach ($provider as $item) {
            if ($item['direction_id'] == 1000) {
                $total += $item[$fieldName];
            }
        }
        return $total;
    }

    /**
     * Вычисляет нагрузку концертмейстера по преподавателям
     * @param $provider
     * @param $teachers_id
     * @return array
     */
    public static function getTeachersLoadMonitor($provider, $teachers_id)
    {
        $data = [];
        $dataArray = ArrayHelper::index(ArrayHelper::toArray($provider), null, ['studyplan_subject_id', 'subject_sect_studyplan_id']);
        foreach ($dataArray as $subject_sect_studyplan_id => $array) {
            foreach ($array as $studyplan_subject_id => $item) {
                $total = [];
                foreach ($item as $index => $itemLoad) {
                    $total[$itemLoad['direction_id']][$itemLoad['teachers_id']] = isset($total[$itemLoad['direction_id']][$itemLoad['teachers_id']]) ? $total[$itemLoad['direction_id']][$itemLoad['teachers_id']] + $itemLoad['load_time'] : $itemLoad['load_time'];
                }
                if (isset($total[1001][$teachers_id])) {
                    if (isset($total[1000])) {
                        foreach ($total[1000] as $teachers => $val) {
                            $data[$teachers] = isset($data[$teachers]) ? $data[$teachers] + $total[1001][$teachers_id] : $total[1001][$teachers_id];
                            break; // если несколько преподавателей в нагрузке.
                        }
                    }
                }
            }
        }
        $arr = [];
        $string = '';
        foreach ($data as $teachers_id => $time_load) {
            $arr[] = RefBook::find('teachers_fio')->getValue($teachers_id) . ' - <b>' . $time_load . '</b>';
        }
        if(!empty($arr)) {
            $string .= '<b>Распределение концертмейстерской нагрузки по преподавателям(ак.ч.):</b> ';
            $string .= implode(', ', $arr);
        }
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return $string;
    }

}
