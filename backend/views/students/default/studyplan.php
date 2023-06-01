<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\studyplan\StudyplanView;
use artsoft\grid\GridPageSize;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Individual plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'studyplan-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'studyplan-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (StudyplanView $model) {
                            return Html::a(sprintf('#%06d', $model->id),
                                Url::to(['/studyplan/default/update', 'id' => $model->id]), [
                                    'title' => 'Перейти в карточку плана',
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'target' => 'blank'
                                ]
                            );
                        },
                        'format' => 'raw',
                        'contentOptions' => function (StudyplanView $model) {
                            return [];
                        },
                    ],
                    'education_programm_name',
                    [
                        'attribute' => 'subject_form_id',
                        'filter' => RefBook::find('subject_form_name')->getList(),
                        'value' => function (StudyplanView $model) {
                            return $model->subject_form_name;
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'course',
                        'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'value' => function (StudyplanView $model) {
                            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'plan_year',
                        'filter' => false,
                        'value' => function (StudyplanView $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [StudyplanView::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                            [StudyplanView::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/students/default',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    Url::to(['/students/default/studyplan', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    Url::to(['/students/default/studyplan', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'view']), [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    Url::to(['/students/default/studyplan', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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


