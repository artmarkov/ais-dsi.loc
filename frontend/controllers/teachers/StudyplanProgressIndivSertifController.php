<?php

namespace frontend\controllers\teachers;

use backend\models\Model;
use common\models\education\AttestationItems;
use common\models\teachers\Teachers;
use Yii;
use yii\db\Exception;
use yii\db\Query;

/**
 * StudyplanProgressIndivSertifController
 */
class StudyplanProgressIndivSertifController extends MainController
{

    public function actionUpdate($objectId)
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $subject_key = base64_decode($objectId);

        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['teachers/studyplan-progress']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

        $model = new AttestationItems();
        $modelsItems = AttestationItems::getAttestationsForTeachers($this->teachers_id, $subject_key);
//             echo '<pre>' . print_r($modelsItems, true) . '</pre>';die();
        if ($model->load(Yii::$app->request->post())) {
            //  $modelsItems = Model::createMultiple(AttestationItems::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = Model::validateMultiple($modelsItems);
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = true;
                    foreach ($modelsItems as $modelItems) {
                        $modelAttestation = $modelItems->id ? AttestationItems::findOne($modelItems->id) : new AttestationItems();
                        $modelAttestation->studyplan_subject_id = $modelItems->studyplan_subject_id;
                        $modelAttestation->plan_year = $modelItems->plan_year;
                        $modelAttestation->lesson_mark_id = $modelItems->lesson_mark_id;
                        $modelAttestation->mark_rem = $modelItems->mark_rem;
                        if (!($flag = $modelAttestation->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->redirect(['/teachers/studyplan-progress-indiv']);
                    }
                } catch
                (Exception $e) {
                    print_r($e->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form-indiv-sertif.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'modelsItems' => $modelsItems,
            'subject_key' => $subject_key,
        ]);

    }


    public function actionDelete($objectId)
    {
        $subject_key = base64_decode($objectId);
        $keyArray = explode('||', $subject_key);
        $subject_key = $keyArray[0];
        $plan_year = $keyArray[1];

        $models = (new Query())->from('lesson_items_progress_studyplan_view')
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->all();
        foreach ($models as $model) {
            $modelSertif = AttestationItems::findOne(['studyplan_subject_id' => $model['studyplan_subject_id'], 'plan_year' => $model['plan_year']]);
            $modelSertif ? $modelSertif->delete() : null;
        }
        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect(Yii::$app->request->referrer);

    }
}