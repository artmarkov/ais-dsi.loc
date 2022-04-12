<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesPlan */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Activities Plans'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-plan-view">
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
'author_id',
'name',
'datetime_in:datetime',
'datetime_out:datetime',
'places',
'auditory_id',
'department_list',
'teachers_list',
'category_id',
'form_partic',
'partic_price',
'visit_flag',
'visit_content:ntext',
'important_flag',
'region_partners:ntext',
'site_url:url',
'site_media',
'description:ntext',
'rider:ntext',
'result:ntext',
'num_users',
'num_winners',
'num_visitors',
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
