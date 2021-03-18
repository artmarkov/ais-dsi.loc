<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use yii\helpers\ArrayHelper;

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
                             echo GridQuickLinks::widget([
                                'model' => Teachers::className(),
                                'searchModel' => $searchModel,
                            ])
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

                    <?=
                    GridView::widget([
                        'id' => 'teachers-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'teachers-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (Teachers $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'teachersFullName',
                                'controller' => '/teachers/default',
                                'title' => function (Teachers $model) {
                                    return Html::a($model->teachersFullName, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'position_id',
                                'value' => 'position.name',
                                'label' => Yii::t('art/teachers', 'Name Position'),
                                'filter' => \common\models\guidejob\Position::getPositionList(),
                            ],
//                            [
//                                'attribute' => 'work_id',
//                                'value' => 'work.name',
//                                'label' => Yii::t('art/teachers', 'Name Work'),
//                                'filter' => \common\models\guidejob\Work::getWorkList(),
//                            ],
                            [
                                'attribute' => 'gridDepartmentSearch',
                                'filter' => Teachers::getDepartmentList(),
                                'value' => function (Teachers $model) {
                                    return implode(', ',
                                        ArrayHelper::map($model->departmentItem, 'id', 'name'));
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
//                            'user.phone',
//                            'user.email',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Teachers::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [Teachers::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:120px']
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


