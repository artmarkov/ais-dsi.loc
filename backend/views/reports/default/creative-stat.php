<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет КПК';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="creative-stat">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-creative', compact('model_date')) ?>
        </div>
    </div>
</div>
