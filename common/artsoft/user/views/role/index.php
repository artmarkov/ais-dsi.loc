<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\models\Role;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\RoleSearch $searchModel
 * @var yii\web\View $this
 */
$this->title = Yii::t('art/user', 'Roles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(['create']); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'role-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php Pjax::begin(['id' => 'role-grid-pjax']) ?>

                    <?=
                    GridView::widget([
                        'id' => 'role-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'role-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')]
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'description',
                                'value' => function (Role $model) {
                                    if (\artsoft\models\User::hasPermission('manageRolesAndPermissions')) {
                                        return Html::a($model->description,
                                            ['view', 'id' => $model->name],
                                            ['data-pjax' => 0]);
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
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->name];
                                },
                                'controller' => '/user/role',
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




















