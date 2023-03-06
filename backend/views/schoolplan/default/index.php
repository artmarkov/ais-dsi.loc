<?php

use artsoft\helpers\RefBook;
use common\models\own\Department;
use common\models\user\UserCommon;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\Schoolplan;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'School Plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schoolplan-plan-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
            <?= $this->render('_search', compact('model_date')) ?>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => ActivitiesPlan::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'schoolplan-plan-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'schoolplan-plan-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'schoolplan-plan-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'schoolplan-plan-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
//                        'rowOptions' => function(Schoolplan $model) {
//                            if($model->doc_status == Schoolplan::DOC_STATUS_CANCEL) {
//                                return ['class' => 'danger'];
//                            }
//                            return [];
//                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Schoolplan $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'contentOptions' => function (Schoolplan $model) {
                                    switch ($model->doc_status) {
                                        case Schoolplan::DOC_STATUS_DRAFT:
                                            return ['class' => 'default'];
                                        case Schoolplan::DOC_STATUS_AGREED:
                                            return ['class' => 'success'];
                                        case Schoolplan::DOC_STATUS_WAIT:
                                            return ['class' => 'warning'];
                                        case Schoolplan::DOC_STATUS_CANCEL:
                                            return ['class' => 'danger'];
                                    }
                                },
                            ],
                            [
                                'attribute' => 'title',
                                'value' => function (Schoolplan $model) {
                                    return $model->title;
                                },
                                'options' => ['style' => 'width:450px'],
                            ],
                            'datetime_in:datetime',
                            'datetime_out:datetime',
                            [
                                'attribute' => 'category_id',
                                'value' => function ($model) {
                                    return $model->categoryName;
                                },
                                'options' => ['style' => 'width:350px', 'class' => 'danger'],
                                'filter' => \common\models\guidesys\GuidePlanTree::getPlanList(),
                            ],
                            'auditory_places',

                            [
                                'attribute' => 'department_list',
                                'filter' => Department::getDepartmentList(),
                                'value' => function (Schoolplan $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Department::findOne($id)->name;
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'executors_list',
                                'filter' => RefBook::find('teachers_fio', UserCommon::STATUS_ACTIVE)->getList(),
                                'value' => function (Schoolplan $model) {
                                    $v = [];
                                    foreach ($model->executors_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('teachers_fio')->getValue($id);
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/schoolplan/default',
                                'template' => '{view} {update} {delete}',
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


