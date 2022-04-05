<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\sigur\UsersCardLog */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Card Logs'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-card-log-view">

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'user_common_id',
                                'key_hex',
                                'datetime',
                                'deny_reason',
                                'dir_code',
                                'dir_name',
                                'evtype_code',
                                'evtype_name',
                                'name',
                                'position',
                            ],
                        ]) ?>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::exitButton(['/logs/sigur/index']);?>
                        <?= \artsoft\helpers\ButtonHelper::deleteButton(['/logs/sigur/delete', 'id' => $model->id]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
