<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\Studyplan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use lo\widgets\modal\ModalAjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-index">

    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(['students/default/studyplan', 'id' => $id, 'mode' => 'create']); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            echo GridQuickLinks::widget([
                                'model' => Studyplan::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
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
                        'bulkActionOptions' => [
                            'gridId' => 'studyplan-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/students/default',
                                'title' => function (Studyplan $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['/studyplan/default/view', 'id' => $model->id, 'objectId' => $model->id, 'mode' => 'view'], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        return  Html::a(Yii::t('art', 'Edit'),
                                            Url::to(['/students/default/studyplan', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'view' => function ($url, $model, $key) {
                                        return  Html::a(Yii::t('art', 'View'),
                                            Url::to(['/students/default/studyplan', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'view']), [
                                                'title' => Yii::t('art', 'View'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a(Yii::t('art', 'Delete'),
                                            Url::to(['/students/default/studyplan/delete', 'id' => $model->student_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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
//                            [
//                                'attribute' => 'student_id',
//                                'value' => function (Studyplan $model) {
//                                    return RefBook::find('students_fullname')->getValue($model->student_id);
//                                },
//                                'format' => 'raw'
//                            ],
                            [
                                'attribute' => 'programmName',
                                'value' => function (Studyplan $model) {
                                    return RefBook::find('education_programm_name')->getValue($model->programm_id);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'plan_year',
                                'value' => function (Studyplan $model) {
                                    return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                                },
                                'format' => 'raw'
                            ],
                            'course',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Studyplan::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [Studyplan::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
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


