<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */
/* @var $modelsEducationProgrammLevel */
/* @var $modelsEducationProgrammLevelSubject */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Programms'), 'url' => ['education/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="education-programm-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsEducationProgrammLevel' => $modelsEducationProgrammLevel,
        'modelsEducationProgrammLevelSubject' => $modelsEducationProgrammLevelSubject,
        'readonly' => $readonly
    ]) ?>
</div>