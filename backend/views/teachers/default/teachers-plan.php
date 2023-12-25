<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Teachers Plan');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-plan-index">
    <div class="panel">
        <div class="panel-heading">
            Планирование индивидуальных занятий: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <?= \artsoft\helpers\ButtonHelper::createButton(); ?>

                    <?php

                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => TeachersPlan::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'teachers-plan-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'teachers-plan-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'teachers-plan-grid',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
               /* 'bulkActionOptions' => [
                    'gridId' => 'teachers-plan-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],*/
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px']],
                    [
                        'attribute' => 'id',
                        'value' => function (TeachersPlan $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],

                    [
                        'attribute' => 'direction_id',
                        'filter' => \common\models\guidejob\Direction::getDirectionList(),
                        'value' => function ($model, $key, $index, $widget) {
                            return $model->direction ? $model->direction->name : null;
                        },

                    ],
                    //  'teachers_id',
                    [
                        'attribute' => 'plan_year',
                        'value' => function (TeachersPlan $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsValue($model->plan_year);
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'half_year',
                        'value' => function (TeachersPlan $model) {
                            return \artsoft\helpers\ArtHelper::getHalfYearValue($model->half_year);
                        },
                        'options' => ['style' => 'width:150px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'planDisplay',
                        'value' => function ($model) {
                            return $model->getPlanDisplay();
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'auditory_id',
                        'filter' => RefBook::find('auditory_memo_1')->getList(),
                        'value' => function ($model) {
                            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                        },
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                        'controller' => '/teachers/teachers-plan',
                        'template' => '{view} {update} {delete}',
                        'visible' => \artsoft\Art::isBackend(),
                        'buttons' => [
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'update' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
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


