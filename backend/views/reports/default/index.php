<?php


/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Табель учета пед.часов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
        </div>
    </div>
</div>
