<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Табель учета посещений занятий учащимися';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan_timesheet-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_studyplan_timesheet_search', compact('model_date')) ?>
        </div>
    </div>
</div>
