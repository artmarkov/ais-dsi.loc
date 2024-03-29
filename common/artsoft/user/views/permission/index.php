<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\models\AuthItemGroup;
use artsoft\models\Permission;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\PermissionSearch $searchModel
 * @var yii\web\View $this
 */

$this->title = Yii::t('art/user', 'Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(['create']); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'permission-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'permission-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'permission-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'permission-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')]
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'description',
                                'value' => function ($model) {
                                    if (\artsoft\models\User::hasPermission('manageRolesAndPermissions')) {
                                        return Html::a($model->description, ['view', 'id' => $model->name],
                                            (($model->name == Yii::$app->art->commonPermissionName)) ?
                                                ['data-pjax' => 0, 'class' => 'label label-primary'] : ['data-pjax' => 0]
                                        );
                                    } else {
                                        return $model->description;
                                    }
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'name',
                                'options' => ['style' => 'width:200px'],
                            ],
                            [
                                'attribute' => 'group_code',
                                'filter' => ArrayHelper::map(AuthItemGroup::find()->asArray()->all(),
                                    'code', 'name'),
                                'value' => function (Permission $model) {
                                    return $model->group_code ? $model->group->name : '';
                                },
                                'options' => ['style' => 'width:300px'],
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->name];
                                },
                                'controller' => '/user/permission',
                                'template' => '{update} {view} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        $options = array_merge([
                                            'title' => Yii::t('art', 'Settings'),
                                            'aria-label' => Yii::t('art', 'Settings'),
                                            'data-pjax' => '0',
                                        ]);
                                        return Html::a('<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>', $url, $options);
                                    }
                                ],
                            ],
                        ],
                    ]);
                    ?>
                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>





