<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\employees\Employees;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $searchModel common\models\employees\search\EmployeesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/employees', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-index">
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
                            /* echo GridQuickLinks::widget([
                                'model' => Employees::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'employees-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'employees-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'employees-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        /*'bulkActionOptions' => [
                            'gridId' => 'employees-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],*/
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn',  'visible' => \artsoft\Art::isBackend(), 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'options' => ['style' => 'width:30px'],
                                'value' => function (Employees $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'fullName',
                                'hidden' => false,
                                'hiddenFromExport'=> true,
                                'options' => ['style' => 'width:100%'],
                                'value' => function (Employees $model) {
                                    return $model->fullName;
                                },
                            ],
                            [
                                'attribute' => 'last_name',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'value' => function (Employees $model) {
                                    return $model->user->last_name;
                                },
                            ],
                            [
                                'attribute' => 'first_name',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'value' => function (Employees $model) {
                                    return $model->user->first_name;
                                },
                            ],
                            [
                                'attribute' => 'middle_name',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'value' => function (Employees $model) {
                                    return $model->user->middle_name ?? '';
                                },
                            ],
                            'position',
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
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'visible' => \artsoft\Art::isBackend(),
                                'controller' => '/employees/default',
                                'template' => '{view} {update}',/* {delete}*/
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'visible' => \artsoft\Art::isFrontend(),
                                'controller' => '/employees/default',
                                'template' => '{view}',
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


