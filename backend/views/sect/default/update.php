<?php

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSect */
/* @var $modelsSubjectSectStudyplan */
/* @var $readonly */

$this->title = Yii::t('art', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="subject-sect-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsSubjectSectStudyplan' => $modelsSubjectSectStudyplan,
        'readonly' => $readonly
    ]) ?>
</div>