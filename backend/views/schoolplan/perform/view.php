<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanPerform */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Performs'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schoolplan-perform-view">
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
'schoolplan_id',
'studyplan_subject_id',
'thematic_items_list',
'lesson_mark_id',
'winner_id',
'resume',
'status_exe',
'status_sign',
'signer_id',
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
</div>
