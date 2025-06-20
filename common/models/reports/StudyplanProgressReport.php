<?php

namespace common\models\reports;

use artsoft\widgets\Tooltip;
use common\models\education\EducationProgramm;
use common\models\education\SummaryProgress;
use yii\helpers\ArrayHelper;

class StudyplanProgressReport
{
    protected $model_date;
    protected $data_mark;
    protected $programm_list;
    protected $course_list = ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8];
    protected $mark_list = ['5' => 'Отлично', '4' => 'Хорошо', '3' => 'Удовлетворительно', 'НА' => 'Неуд./Не аттестован', '0' => 'Нет оценки'];
    protected $programmIds;

    public function __construct($model_date)
    {
        $this->model_date = $model_date;
        $this->programmIds = $model_date->programm_id;
        $this->model_date->finish_flag = false;
        $this->data_mark = $this->getDataMark();
        $this->programm_list = $this->getProgrammList(array_keys($this->data_mark));
//        echo '<pre>' . print_r($this->getDataMark(), true) . '</pre>';
//        die();
    }

    /**
     * @param bool $programmIds
     * @return array
     */
    protected function getProgrammList($programmIds = false)
    {
        if (!$programmIds) {
            return [];
        }
        $models = EducationProgramm::find()
            ->select('id, short_name as name')
            ->where(['status' => EducationProgramm::STATUS_ACTIVE]);
        if ($programmIds) $models = $models->andWhere(['id' => $programmIds]);
        $models = $models->orderBy('short_name')
            ->asArray()->all();

        return ArrayHelper::map($models, 'id', 'name');
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProgressData()
    {
        $dataProgress = new SummaryProgress($this->model_date);
        return $dataProgress->getData(true)['data'];
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function getData()
    {
        $attributes = [
//            'id' => 'Id',
            'name' => 'Программа',
            'count_mark' => 'Кол-во',
            'mark' => 'Оценка',
        ];
        $attributes += $this->course_list;

        $data = [];
        $all_summ = ['5' => 0, '4' => 0, '3' => 0, 'НА' => 0, '0' => 0];

        $i = 0;
        foreach ($this->programm_list as $programm_id => $programm_name) {
            foreach ($this->mark_list as $markId => $mark) {
                foreach ($this->course_list as $courseId => $course) {
                    //  $data[$i]['id'] = $programm_id;
                    $count = isset($this->data_mark[$programm_id][$markId][$courseId]) ? count($this->data_mark[$programm_id][$markId][$courseId]) : 0;
                    $data[$i]['name'] = $programm_name;
                    $data[$i]['mark'] = $mark;
                    $data[$i][$courseId] =  $count != 0 ? $count . $this->getNotice($this->data_mark[$programm_id][$markId][$courseId], $markId) : '-';
                    $data[$i]['count_mark'] = isset($data[$i]['count_mark']) ? $data[$i]['count_mark'] + $count : $count;
                    $all_summ[$markId] += $count;
                }
                $i++;
            }
        }
        return ['data' => $data, 'all_summ' => $all_summ, 'course_list' => $this->course_list, 'attributes' => $attributes];
    }

    /**
     * @return array
     */
    public function getDataMark()
    {
        $data = [];
        foreach ($this->getProgressData() as $courseId => $model) {
            $data[] = [
                'studyplan_id' => $model['studyplan_id'],
                'student_id' => $model['student_id'],
                'student_fio' => $model['student_fio'],
                'plan_year' => $model['plan_year'],
                'programm_id' => $model['programm_id'],
                'education_cat_short_name' => $model['education_cat_short_name'],
                'education_programm_short_name' => $model['education_programm_short_name'],
                'subject_form_name' => $model['subject_form_name'],
                'course' => $model['course'],
                'mark' => $this->getMark($model['marks']),

            ];
        }
        return ArrayHelper::index($data, null, ['programm_id', 'mark', 'course']);
    }

    /**
     * @param $marks
     * @return string
     */
    protected function getMark($marks)
    {

        switch ($marks) {
            case in_array('', $marks) :
            case in_array('*', $marks) :
                $mark = '0';
                break;
            case in_array('2', $marks) :
            case in_array('НА', $marks) :
                $mark = 'НА';
                break;
            case in_array('3-', $marks) :
            case in_array('3', $marks) :
            case in_array('3+', $marks) :
                $mark = '3';
                break;
            case in_array('4-', $marks) :
            case in_array('4', $marks) :
            case in_array('4+', $marks) :
                $mark = '4';
                break;
            default :
                $mark = '5';
        }
        return $mark;
    }

    /**
     * @param $models
     * @param $markId
     * @return string
     * @throws \Throwable
     */
    public function getNotice($models, $markId)
    {
        $studentsFio = [];

        if (!isset($models)) return '';

        foreach ($models as $id => $model) {
            $studentsFio[] = $model['student_fio'];
        }
        $message = implode(', ', $studentsFio);
        $tooltip = Tooltip::widget(['type' => $markId == 'Нет оценки' ? 'danger' : 'info', 'message' => $message]);

        return $tooltip;
    }
}
