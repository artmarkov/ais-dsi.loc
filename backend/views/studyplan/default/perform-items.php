<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\studyplan\StudyplanThematicItems;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\SchoolplanPerform;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanPerformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model */

?>
<div class="schoolplan-perform-index">
    <div class="panel">
        <div class="panel-heading">
            Выполнение плана и участие в мероприятиях: <?= RefBook::find('students_fullname')->getValue($model->student_id); ?>
            <?= $model->getProgrammName() . ' - ' . $model->course . ' класс.'; ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search-studyplan', compact('model_date')) ?>
            <?=  User::hasRole(['teacher', 'department']) ?  \artsoft\helpers\ButtonHelper::createButton() : null; ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => SchoolplanPerform::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'schoolplan-perform-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'schoolplan-perform-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'schoolplan-perform-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
//                'bulkActionOptions' => [
//                    'gridId' => 'schoolplan-perform-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
//                ],
                'columns' => [
                    /* ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],*/
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'schoolplan_id',
                        'value' => function ($model) {
                            return isset($model->schoolplan) ? $model->schoolplan->title : '';
                        },
                    ],
                    [
                        'attribute' => 'teachers_id',
                        'value' => function ($model) {
                            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
                        },
                    ],
                    [
                        'attribute' => 'studyplan_subject_id',
                        'value' => function ($model) {
                            return RefBook::find('subject_memo_4')->getValue($model->studyplan_subject_id);
                        },
                    ],
                    [
                        'attribute' => 'thematic_items_list',
                        'value' => function ($model) {
                            if (!empty($model->thematic_items_list[0])) {
                                $thematic_items_list = StudyplanThematicItems::find()->select('topic')->where(['id' => $model->thematic_items_list])->column();
                                return implode(', ', $thematic_items_list);
                            } else {
                                return $model->task_ticket ?? '';
                            }
                        },
                        'label' => 'Задание',
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'lesson_mark_id',
                        'value' => function ($model) {
                            return $model->lessonMark ? $model->lessonMark->mark_label : '';
                        },
                    ],
                    [
                        'attribute' => 'winner_id',
                        'value' => function ($model) {
                            return $model->getWinnerValue($model->winner_id);
                        },
                    ],

                    'resume',

                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status_exe',
                        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusExeOptionsList(),
                        'options' => ['style' => 'width:100px'],
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status_sign',
                        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusSignOptionsList(),
                        'options' => ['style' => 'width:100px'],
                        'visible' => Yii::$app->settings->get('mailing.schoolplan_perform_doc')
                    ],
                    [
                        'attribute' => 'signer_id',
                        'filter' => false,
                        'value' => function (SchoolplanPerform $model) {
                            return isset($model->user->userCommon) ? $model->user->userCommon->lastFM : $model->signer_id;
                        },
                        'options' => ['style' => 'width:150px'],
                        'contentOptions' => ['style' => "text-align:center; vertical-align: middle;"],
                        'format' => 'raw',
                        'visible' => Yii::$app->settings->get('mailing.schoolplan_perform_doc')
                    ],
                    [
                        'value' => function (SchoolplanPerform $model) {
                            return artsoft\fileinput\widgets\FileInput::widget([
                                'model' => $model,
                                'pluginOptions' => [
                                    'deleteUrl' => false,
                                    'showRemove' => false,
                                    'showCaption' => false,
                                    'showBrowse' => false,
                                    'showUpload' => false,
                                    'dropZoneEnabled' => false,
                                    'showCancel' => false,
                                    'initialPreviewShowDelete' => false,
                                    'fileActionSettings' => [
                                        'showDrag' => false,
                                        'showRotate' => false,
                                    ],
                                ],
                            ]);
                        },
                        'label' => 'Файл',
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'visible' => User::hasRole(['teacher', 'department']),
                        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                        'width' => '90px',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    [\artsoft\Art::isBackend() ? '/studyplan/default/studyplan-perform' : '/teachers/studyplan/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    [\artsoft\Art::isBackend() ? '/studyplan/default/studyplan-perform' : '/teachers/studyplan/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    [\artsoft\Art::isBackend() ? '/studyplan/default/studyplan-perform' : '/teachers/studyplan/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                        'visibleButtons' => [
                            'view' => function ($model) {
                                return true;
                            },
                            'update' => function ($model) {
                                return User::hasRole(['teacher', 'department']) ? true : false;
                            },
                            'delete' => function ($model) {
                                 return User::hasRole(['teacher', 'department']) ? true : false;
                            },
                        ]
                    ],

                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>


