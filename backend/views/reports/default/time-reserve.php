<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Резерв учебного времени аудиторий';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-reserve-stat">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-time-reserve', compact('model_date')) ?>
        </div>
    </div>
</div>
