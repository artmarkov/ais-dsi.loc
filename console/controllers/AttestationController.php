<?php

namespace console\controllers;

use common\models\education\AttestationItems;
use common\models\education\LessonItemsProgressView;
use common\models\education\LessonTest;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii attestation
 *
 * @author markov-av
 */
class AttestationController extends Controller
{

    public function actionIndex()
    {
        foreach ($this->getLessonItemsProgress() as $item => $model) {
            $modelAttestation = AttestationItems::findOne(['studyplan_subject_id' => $model->studyplan_subject_id, 'plan_year' => $model->plan_year]) ?? new AttestationItems();
            $modelAttestation->studyplan_subject_id = $model->studyplan_subject_id;
            $modelAttestation->plan_year = $model->plan_year;
            $modelAttestation->lesson_mark_id = $model->lesson_mark_id;
            $modelAttestation->mark_rem = $model->mark_rem;
            if ($modelAttestation->save(false)) {
                $this->stdout('Добавлена оценка ПА : ' . $modelAttestation->id . " ", Console::FG_GREY);
                $this->stdout("\n");
            } else {
                $this->stdout('Ошибка добавления ПА : ' . $modelAttestation->id . " ", Console::FG_RED);
                $this->stdout("\n");
            }
        }
    }

    protected function getLessonItemsProgress()
    {
        $models = LessonItemsProgressView::find()
            ->where(['IS NOT','lesson_mark_id', NULL])
            ->andWhere(['AND',
                    ['test_category' => LessonTest::MIDDLE_ATTESTATION],
                    ['med_cert' => true]
            ])
            ->all();
        return $models;
    }

}
