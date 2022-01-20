<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersLoad */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Loads'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-load-view">

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
                                'direction_id',
                                'teachers_id',
                                'week_time',
                                'created_at',
                                'created_by',
                                'updated_at',
                                'updated_by',
                                'version',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
