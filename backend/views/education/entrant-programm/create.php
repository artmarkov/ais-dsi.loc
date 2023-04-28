<?php


/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantProgramm */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Programms'), 'url' => ['education/entrant-programm/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="entrant-programm-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>