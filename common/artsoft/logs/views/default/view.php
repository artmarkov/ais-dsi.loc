<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\models\UserVisitLog $model
 */

$this->title = Yii::t('art/user', 'Log №{id}', ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Visit Log'), 'url' => ['/logs/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-visit-log-view">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                <div class="col-md-12">

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'user_id',
                                'value' => @$model->user->username,
                            ],
                            [
                                'attribute' => 'visit_time',
                                'value' => $model->visitDatetime
                                    . ' ' . $model->geoLocation['city']['name_en']
                                    . ' ' . $model->geoLocation['country']['name_en'],
                            ],
                            'ip',
                            'language',
                            'os',
                            'browser',
                            'user_agent',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::exitButton('/logs/default/index');?>
                <?= \artsoft\helpers\ButtonHelper::deleteButton($model, ['/logs/default/delete', 'id' => $model->id]);?>
            </div>
        </div>
    </div>
</div>
