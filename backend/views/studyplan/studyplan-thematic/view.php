<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanThematic */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Thematics'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-thematic-view">

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
'subject_sect_studyplan_id',
'studyplan_subject_id',
'thematic_category',
'period_in',
'period_out',
'template_flag',
'template_name',
'created_at',
'created_by',
'updated_at',
'updated_by',
                ],
            ])?>
                </div>
            </div>
        </div>
    </div>
</div>
