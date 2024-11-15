<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'График работы преподавателей';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-generator">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-generator', compact('model_date')) ?>
        </div>
    </div>
</div>
