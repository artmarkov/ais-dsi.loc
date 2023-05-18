<?php

use artsoft\helpers\RefBook;
use common\models\own\Department;
use common\models\studyplan\Studyplan;
use common\models\subject\Subject;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="entrant-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Entrant::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (Entrant $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'student_id',
                        'filter' => RefBook::find('students_fullname')->getList(),
                        'value' => function (Entrant $model) {
                            return RefBook::find('students_fullname')->getValue($model->student_id);
                        },
                        'format' => 'raw'
                    ],
//            'comm_id',
                    [
                        'attribute' => 'group_id',
                        'filter' =>  \common\models\entrant\Entrant::getCommGroupList($id),
                        'value' => function (\common\models\entrant\Entrant $model) use ($id){
                            return \common\models\entrant\Entrant::getCommGroupValue($id, $model->group_id);
                        },
                        'options' => ['style' => 'width:250px'],
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'subject_list',
                        'filter' => RefBook::find('subject_name')->getList(),
                        'value' => function (Entrant $model) {
                            $v = [];
                            foreach ($model->subject_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = Subject::findOne($id)->name;
                            }
                            return implode('<br/> ', $v);
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'last_experience',
                    [
                        'attribute' => 'mid_mark',
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return round($model->mid_mark, 2);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'decision_id',
                        'optionsArray' => [
                            [0, 'Не обработано', 'default'],
                            [1, 'Рекомендован', 'success'],
                            [2, 'Не рекомендован', 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [0, 'В ожидании испытаний', 'default'],
                            [1, 'Испытания открыты', 'success'],
                            [2, 'Испытания завершены', 'warning'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'view']), [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'delete']), [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
//                        'visibleButtons' => [
//                            'create' => function ($model) {
//                                return true;
//                            },
//                            'delete' => function ($model) {
//                                return false;
//                            },
//                            'update' => function ($model) {
//                                return true;
//                            }
//                        ]
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


