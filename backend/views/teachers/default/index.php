<?php

use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\own\Department;
use common\models\guidejob\Bonus;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            //                            echo GridQuickLinks::widget([
                            //                                'model' => Teachers::className(),
                            //                                'searchModel' => $searchModel,
                            //                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'teachers-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'teachers-grid-pjax',
                    ])
                    ?>
                    <?= GridView::widget([
                        'id' => 'teachers-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'teachers-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'rowOptions' => function (Teachers $model) {
                            if ($model->userStatus == UserCommon::STATUS_INACTIVE) {
                                return ['class' => 'danger'];
                            }
                            return [];
                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn',  'visible' => \artsoft\Art::isBackend(), 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'options' => ['style' => 'width:30px'],
                                'value' => function (Teachers $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'fullName',
                                'value' => function (Teachers $model) {
                                    return $model->fullName;
                                },
                            ],
                            [
                                'attribute' => 'position_id',
                                'value' => 'position.name',
                                'label' => Yii::t('art/teachers', 'Name Position'),
                                'filter' => \common\models\guidejob\Position::getPositionList(),
                                'options' => ['style' => 'width:350px'],
                            ],
                            [
                                'attribute' => 'department_list',
                                'filter' => Department::getDepartmentList(),
                                'value' => function (Teachers $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Department::findOne($id)->name ?? null;
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'bonus_list',
                                'filter' => Bonus::getBonusList(),
                                'value' => function (Teachers $model) {
                                    $v = [];
                                    foreach ($model->bonus_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Bonus::findOne($id)->name;
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'bonus_summ',
                            'bonus_summ_abs',
                            'tab_num',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'userStatus',
                                'optionsArray' => [
                                    [UserCommon::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [UserCommon::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                                ],
                                'options' => ['style' => 'width:120px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'access_work_flag',
                                'optionsArray' => [
                                    [1, 'Да', 'success'],
                                    [0, 'Нет', 'danger'],
                                ],
                                'label' => 'Доступ к работе',
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'visible' => \artsoft\Art::isBackend(),
                                'template' => '{view} {update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'buttons' => [
                                    'view' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                            ['/teachers/default/view', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'View'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'update' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                           ['/teachers/default/update', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                            ['/teachers/default/delete', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Delete'),
                                                'aria-label' => Yii::t('art', 'Delete'),
                                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                ],
//
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'visible' => \artsoft\Art::isFrontend(),
                                'template' => '{view}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'buttons' => [
                                    'view' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                            [User::hasRole(['reestrFrontend']) ? '/reestr/teachers/view' : '/execution/teachers/view', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'View'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
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


