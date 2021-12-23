<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */
/* @var $modelsEducationProgrammLevel */
/* @var $modelsEducationProgrammLevelSubject */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Programms'), 'url' => ['education/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-programm-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsEducationProgrammLevel' => $modelsEducationProgrammLevel,
        'modelsEducationProgrammLevelSubject' => $modelsEducationProgrammLevelSubject,
        'readonly' => $readonly
    ]) ?>
</div>