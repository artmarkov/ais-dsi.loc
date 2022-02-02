<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\ConsultSchedule */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedules'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consult-schedule-view">

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
'teachers_load_id',
'datetime_in:datetime',
'datetime_out:datetime',
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
