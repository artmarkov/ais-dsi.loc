<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studygroups\SubjectSect */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-view">

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
'plan_year',
'programm_id',
'course',
'subject_cat_id',
'subject_id',
'subject_type_id',
'subject_vid_id',
'sect_name',
'studyplan_list:ntext',
'week_time',
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
