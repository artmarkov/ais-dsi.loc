<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Проект расписания занятий';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-project-stat">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-schedule-project', compact('model_date')) ?>
        </div>
    </div>
</div>
