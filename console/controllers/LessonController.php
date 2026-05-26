<?php

namespace console\controllers;

use common\models\education\LessonItems;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii lesson
 *
 */
class LessonController extends Controller
{
    public function actionIndex()
    {
        ini_set('memory_limit', '1024M');

        /*$models = \Yii::$app->db->createCommand('SELECT DISTINCT teachers_id, lesson_items_id, lesson_date, s.time_in, s.time_out, s.auditory_id FROM lesson_items_progress_studyplan_view l
             JOIN subject_schedule s ON s.teachers_load_id = l.teachers_load_id
                 AND s.week_day = DATE_PART(\'dow\', to_timestamp(l.lesson_date+10800))
             WHERE plan_year = 2025 AND direction_id = 1000
             ORDER BY lesson_date')->queryAll();*/
        $models = \Yii::$app->db->createCommand('SELECT DISTINCT teachers_id, lesson_items_id, lesson_date, hh.time_in, hh.time_out, hh.auditory_id FROM lesson_items_progress_studyplan_view l
	    JOIN LATERAL (WITH ss AS (
    SELECT id, MAX(hist_id) as hist_id FROM subject_schedule_hist 
		        WHERE updated_at <= l.created_at AND teachers_load_id = l.teachers_load_id 
	GROUP BY id	
	) SELECT * FROM subject_schedule_hist h 
	WHERE h.hist_id in (SELECT hist_id FROM ss))
		   hh 
		ON hh.teachers_load_id = l.teachers_load_id
             WHERE plan_year = 2025 AND direction_id = 1000 AND l.datetime_in is null 
	AND hh.week_day = DATE_PART(\'dow\', to_timestamp(l.lesson_date+10800))
             ')->queryAll();
               /* echo '<pre>' . print_r($models, true) . '</pre>';
        die();*/


        $count = count($models);
        foreach ($models as $item => $model) {
            $modelLesson = LessonItems::findOne(['id' => $model['lesson_items_id']]);
            if($modelLesson) {
                $modelLesson->teachers_id = $modelLesson->teachers_id ? $modelLesson->teachers_id : $model['teachers_id'];
                $modelLesson->datetime_in = $modelLesson->datetime_in ? $modelLesson->datetime_in : $model['lesson_date'] + $model['time_in'];
                $modelLesson->datetime_out = $modelLesson->datetime_out ? $modelLesson->datetime_out : $model['lesson_date'] + $model['time_out'];
                $modelLesson->auditory_id = $modelLesson->auditory_id ? $modelLesson->auditory_id : $model['auditory_id'];
                $modelLesson->save(false);
                $this->stdout('Ok: ' . $modelLesson->id . '-' . $item . '(' . $count . ')', Console::FG_BLUE);
                $this->stdout("\n");
            } else {
                $this->stdout('Error: ' . $model['lesson_items_id'], Console::FG_PURPLE);
                $this->stdout("\n");
            }
        }
    }

}
