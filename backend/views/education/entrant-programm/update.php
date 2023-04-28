<?php


/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantProgramm */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Programms'), 'url' => ['education/entrant-programm/index']];
$this->params['breadcrumbs'][] =  sprintf('#%06d', $model->id);
?>
<div class="entrant-programm-update">
    <?= $this->render('_form', compact('model')) ?>
</div>