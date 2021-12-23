<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSect */
/* @var $modelsSubjectSectStudyplan */
/* @var $modelsTeachersLoad */
/* @var $readonly */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="subject-sect-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsSubjectSectStudyplan' => $modelsSubjectSectStudyplan,
        'modelsTeachersLoad' => $modelsTeachersLoad,
        'readonly' => $readonly
    ]) ?>
</div>