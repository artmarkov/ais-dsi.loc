<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Контингент учащихся';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-stat">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-studyplan-stat', compact('model_date')) ?>
        </div>
    </div>
</div>
