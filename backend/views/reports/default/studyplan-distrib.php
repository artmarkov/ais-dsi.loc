<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Форма №1';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-distrib">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-studyplan-distrib', compact('model_date')) ?>
        </div>
    </div>
</div>
