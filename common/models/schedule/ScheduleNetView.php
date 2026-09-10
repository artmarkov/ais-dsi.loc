<?php

namespace common\models\schedule;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Tooltip;
use common\models\auditory\Auditory;
use common\models\studyplan\Studyplan;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ScheduleNetView
 * @package common\models\schedule
 *
 */
class ScheduleNetView
{
    protected $plan_year;
    protected $teachers_id;
    protected $auditory_id;
    protected $course;
    protected $education_cat_id;
    protected $programm_id;
    protected $studyplanSubjects;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->teachers_id = $model_date->teachers_id;
        $this->auditory_id = $model_date->auditory_id;
        $this->course = $model_date->course;
        $this->education_cat_id = $model_date->education_cat_id;
        $this->programm_id = $model_date->programm_id;
        $this->studyplanSubjects = $this->getStudyplanSubjectAll();

//        echo '<pre>' . print_r($this->getStudyplanSubjectAll(), true) . '</pre>';

    }


    /**
     * Запрос на расписание преподавателя
     * @param $teachersIds
     * @return array
     */
    protected function getTeachersScheduleModels()
    {
        $models = (new Query())->from('schedule_net_view')
            ->where(['=', 'plan_year', $this->plan_year])
            ->andWhere(['IS NOT', 'auditory_id', null])/*->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass()])*/
        ;
        if ($this->teachers_id) {
            $models = $models->andWhere(['=', 'teachers_id', $this->teachers_id]);
        }
        if ($this->auditory_id) {
            $models = $models->andWhere(['=', 'auditory_id', $this->auditory_id]);
        }
        if ($this->course) {
            $models = $models->andWhere(['=', 'course', $this->course]);
//                $models = $models->andWhere(['OR', ['=', 'course', $model_date->course], ['IS', 'course', NULL]]);
        }
        if ($this->programm_id) {
            $programm_list = implode(',', $this->programm_id);
            $models = $models->andWhere(new Expression("string_to_array(programm_list, ','::text)::text[] && string_to_array('{$programm_list}', ','::text)::text[]")); // сравнение массивов
        }
        $models = $models->all();
//        echo '<pre>' . print_r($models, true) . '</pre>';        die();
        return $models;
    }

    public function getTeachersSchedule()
    {
        $models = $this->getTeachersScheduleModels();
        $data = ArrayHelper::index($models, null, ['auditory_id', 'week_day', 'time_in']);
        return $data;
    }

    public function getTeachersAuditory()
    {
        $models = $this->getTeachersScheduleModels();
        $data = ArrayHelper::getColumn($models, 'auditory_id');
        return $data;
    }

    public function getStudyplanSubjectAll()
    {
        $models = $this->getTeachersScheduleModels();
        $data = ArrayHelper::getColumn($models, 'studyplan_subject_list');
        $data = implode(',', $data);
        $data = explode(',', $data);
        $data = array_filter($data);
        $data = array_unique($data);
        $studyplan_subject_list = implode(',', $data);
        $studentsFio = (new \yii\db\Query())->select('studyplan_subject_id,student_fullname')->from('studyplan_subject_view')->distinct()
            ->where(new \yii\db\Expression("studyplan_subject_id = any (string_to_array('{$studyplan_subject_list}', ',')::int[])"))
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])->orderBy('student_fullname')
            ->all();
//        echo '<pre>' . print_r($studentsFio, true) . '</pre>';        die();
        return ArrayHelper::map($studentsFio, 'studyplan_subject_id', 'student_fullname');
    }

    public function getAuditoryModels()
    {
        $modelsAuditory = Auditory::find()->joinWith('cat')->where(['=', 'study_flag', true])->andWhere(['cat_id' => [1000, 1001, 1002]]);
        $modelsAuditory = $modelsAuditory->andWhere(['auditory.id' => $this->getTeachersAuditory()]);
        $modelsAuditory = $modelsAuditory->orderBy(['sort_order' => SORT_ASC])->all();

        return $modelsAuditory;
    }


    public static function getSectNotice($subject_sect_studyplan_id, $studyplan_subject_list, $studyplanSubjects)
    {
        $tooltip = [];
        $studentsFio = [];
        if ($subject_sect_studyplan_id !== 0) {
            if ($studyplan_subject_list == '') {
//                $message = 'Группа ' . RefBook::find('sect_name_2')->getValue($this->subject_sect_studyplan_id) . ' не заполнена';
                $message = 'Группа не заполнена';
                //Notice::registerWarning($message);
                $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
            } else {
                $studyplan_subject_list = explode(',', $studyplan_subject_list);
                foreach ($studyplan_subject_list as $item => $studyplan_subject_id) {
                    $studentsFio[] = $studyplanSubjects[$studyplan_subject_id] ?? '';
                }
                $message = 'Группа (' . count($studentsFio) . '): ' . implode(', ', $studentsFio);
                $tooltip[] = Tooltip::widget(['type' => 'info', 'message' => $message]);
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

}