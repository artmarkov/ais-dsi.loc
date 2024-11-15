<?php
use artsoft\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */

$this->title = 'Выписка из учебного плана';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="student-history">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-student-history', compact('model_date')) ?>
        </div>
        <div class="panel-footer">
            <?= Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['/reports/default/student-history-excel', 'id' => $model_date->studyplan_id], [
                'title' => 'Выгрузить в Excel',
                'class' => 'btn btn-info',
                'data-method' => 'post',
                'data-pjax' => '0',
                'disabled' => !$model_date->studyplan_id
            ]);
            ?>
        </div>
    </div>
</div>
