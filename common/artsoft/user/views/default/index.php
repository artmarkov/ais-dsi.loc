<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridQuickLinks;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\models\Role;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\models\User;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\UserSearch $searchModel
 */
$this->title = Yii::t('art/user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(['create']); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= GridQuickLinks::widget([
                                'model' => User::class,
                                'searchModel' => $searchModel,
                                'options' => [
                                    ['label' => Yii::t('art', 'All'), 'filterWhere' => []],
                                    ['label' => Yii::t('art', 'Active'), 'filterWhere' => ['status' => 10]],
                                    ['label' => Yii::t('art', 'Inactive'), 'filterWhere' => ['status' => 0]],
                                    ['label' => Yii::t('art', 'Banned'), 'filterWhere' => ['status' => -1]],
                                ]
                            ]) ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'user-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'user-grid-pjax',
                    ])
                    ?>

                    <?= GridView::widget([
                        'id' => 'user-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'user-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (User $model) {
                                    if (User::hasPermission('editUsers')) {
                                        return Html::a(sprintf('#%06d', $model->id), ['/user/default/update', 'id' => $model->id], ['data-pjax' => 0]);
                                    } else {
                                        return sprintf('#%06d', $model->id);
                                    }
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'username',
                                'controller' => '/user/default',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'title' => function (User $model) {
                                    if (User::hasPermission('editUsers')) {
                                        return Html::a($model->username, ['/user/default/update', 'id' => $model->id], ['data-pjax' => 0]);
                                    } else {
                                        return $model->username;
                                    }
                                },
                                'buttonsTemplate' => '{update} {delete} {permissions} {password}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        return Html::a(Yii::t('art', 'Edit'),
                                            ['/user/default/update', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-pjax' => '0'
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return !$model->user_common_id ? Html::a(Yii::t('art', 'Delete'),
                                            Url::to(['delete', 'id' => $model->id]), [
                                                'title' => Yii::t('art', 'Delete'),
                                                'aria-label' => Yii::t('art', 'Delete'),
                                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        ) : Yii::t('art', 'Delete');
                                    },
                                    'permissions' => function ($url, $model, $key) {
                                        return Html::a(Yii::t('art/user', 'Permissions'),
                                            ['user-permission/set', 'id' => $model->id], [
                                                'title' => Yii::t('art/user', 'Permissions'),
                                                'data-pjax' => '0'
                                            ]
                                        );
                                    },
                                    'password' => function ($url, $model, $key) {
                                        return Html::a(Yii::t('art/user', 'Password'),
                                            ['/user/default/change-password', 'id' => $model->id], [
                                                'title' => Yii::t('art/user', 'Password'),
                                                'data-pjax' => '0'
                                            ]
                                        );
                                    }
                                ],
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'user_category_name',
                            [
                                'attribute' => 'user_name',
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'attribute' => 'email',
                                'format' => 'raw',
                                'visible' => User::hasPermission('viewUserEmail'),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'email_confirmed',
                                'visible' => User::hasPermission('viewUserEmail'),
                            ],
                            [
                                'attribute' => 'roles',
                                'filter' => ArrayHelper::map(Role::getAvailableRoles(true),
                                    'description', 'description'),
                                'value' => function (User $model) {
                                    return $model->roles;
                                },
                                'format' => 'raw',
                                'visible' => User::hasPermission('viewUserRoles'),
                            ],
                            [
                                'attribute' => 'registration_ip',
                                'value' => function (User $model) {
                                    return Html::a($model->registration_ip,
                                        "http://ipinfo.io/" . $model->registration_ip,
                                        ["target" => "_blank"]);
                                },
                                'format' => 'raw',
                                'visible' => User::hasPermission('viewRegistrationIp'),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'superadmin',
                                'visible' => Yii::$app->user->isSuperadmin,
                                'options' => ['style' => 'width:60px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [User::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [User::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                    [User::STATUS_BANNED, Yii::t('art', 'Banned'), 'danger'],
                                ],
                                'options' => ['style' => 'width:60px']
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
