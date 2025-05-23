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
            <?= $this->render('_search-progress-history', compact('model_date')) ?>
        </div>

    </div>
</div>
