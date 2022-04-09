<?php

use common\models\service\UsersCardLog;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCardLog */

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
                                'name',
                                'position',
                                'key_hex',
                                'datetime',
                                [
                                    'attribute' => 'dir_code',
                                    'value' => function (UsersCardLog $model) {
                                        $arr = [
                                            1 => 'Выход',
                                            2 => 'Вход',
                                            3 => 'Неизвестное'
                                        ];
                                        return $arr[$model->dir_code];
                                    },
                                ],
                                [
                                    'attribute' => 'evtype_code',
                                    'value' => function (UsersCardLog $model) {
                                        $arr = [
                                            1 => 'Проход',
                                            2 => 'Запрет',
                                        ];
                                        return $arr[$model->evtype_code];
                                    },
                                ],
                                [
                                    'attribute' => 'deny_reason',
                                    'value' => function (UsersCardLog $model) {
                                        return (int)$model->deny_reason != null ? '<b style="color: darkred;"> ' . $model->deny_reason . '</b> [' . UsersCardLog::DENY_REASON[(int)$model->deny_reason] . ']' : '';
                                    },
                                    'format' => 'raw'
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::exitButton(['/service/sigur/index']); ?>
                        <?= \artsoft\helpers\ButtonHelper::deleteButton(['/service/sigur/delete', 'id' => $model->id]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
