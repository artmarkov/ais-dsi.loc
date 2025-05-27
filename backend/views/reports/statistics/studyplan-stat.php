<?php

use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика по учебной работе';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="studyplan-stat">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <?= $this->title ?>
                </div>
                <div class="panel-body">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info-circle"></i> Test',
                        'options' => ['class' => 'alert-success'],
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-12">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
