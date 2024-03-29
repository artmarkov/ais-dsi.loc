<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\AuthItemGroupSearch $searchModel
 */
$this->title = Yii::t('art/user', 'Permission Groups');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-groups-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(['create']); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'permission-groups-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'permission-groups-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'permission-groups-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'permission-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')]
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'name',
//                                'class' => 'artsoft\grid\columns\TitleActionColumn',
//                                'controller' => '/user/permission-groups',
                                'value' => function ($model) {
                                    if (User::hasPermission('manageRolesAndPermissions')) {
                                        return Html::a(
                                            $model->name, ['update', 'id' => $model->code],
                                            ['data-pjax' => 0]
                                        );
                                    } else {
                                        return $model->name;
                                    }

                                },
                                'format' => 'raw'
                            ],
                            'code',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->code];
                                },
                                'controller' => '/user/permission-groups',
                                'template' => '{update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
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

































