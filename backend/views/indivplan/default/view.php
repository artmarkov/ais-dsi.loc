<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['indivplan/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indivplan-view">

<div class="panel">
    <div class="panel-heading">
        <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::viewButtons($model) ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
'direction_id',
'teachers_id',
'plan_year',
'week_num',
'week_day',
'time_plan_in:datetime',
'time_plan_out:datetime',
'auditory_id',
'description',
'created_at',
'created_by',
'updated_at',
'updated_by',
'version',
                ],
            ])?>
                </div>
            </div>
        </div>
    </div>
</div>
