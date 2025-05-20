<?php

use artsoft\helpers\RefBook;
use common\models\studyplan\StudyplanThematicItems;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\schoolplan\SchoolplanProtocol;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanProtocolSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_confirm \common\models\schoolplan\SchoolplanProtocolConfirm */

$teachers_list = RefBook::find('teachers_fio')->getList();
$studyplan_subject_list = RefBook::find('subject_memo_4')->getList();
$schoolplan = \common\models\schoolplan\Schoolplan::findOne($id);
?>
<div class="protocol-index">
    <div class="panel">
        <div class="panel-body">
            <?php if ($schoolplan): ?>
                <div class="panel">
                    <div class="panel-heading">
                        Протокол аттестационной комиссии
                    </div>
                    <div class="panel-body">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $schoolplan,
                            'attributes' => [
                                'title',
                                'datetime_in',
                                'datetime_out',
                            ],
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
            <?= $this->render('_confirm', ['model_confirm' => $model_confirm, 'readonly' => (\artsoft\Art::isFrontend() && !$model_confirm->schoolplan->isProtocolSigner()) ? true : false]) ?>
            <div class="row">
                <div class="panel">
                    <div class="panel-heading">
                    <?php if($schoolplan->protocol_subject_id[0]): ?>
                        <?= \artsoft\Art::isBackend() || /*($model_confirm->schoolplan->isExecutors() &&*/ in_array($model_confirm->confirm_status, [0, 3]) ? \artsoft\helpers\ButtonHelper::createButton() : null; ?>

                    <?php endif;?>
                    </div>
                    <div class="panel-body">
                        <?php if(!$schoolplan->protocol_subject_id[0]): ?>
                            <?php echo \yii\bootstrap\Alert::widget([
                                'body' => '<i class="fa fa-info-circle"></i> Заполните аттестационную комиссию в карточке мероприятия.',
                                'options' => ['class' => 'alert-danger'],
                            ]);
                            ?>
                        <?php endif;?>
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => SchoolplanProtocol::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'protocol-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'protocol-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'protocol-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => \artsoft\Art::isBackend() ? [
                            'gridId' => 'protocol-grid',
                            'actions' => [\yii\helpers\Url::to(['/schoolplan/default/protocol-bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ] : false,
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'visible' => \artsoft\Art::isBackend()],
                            [
                                'attribute' => 'id',
                                'value' => function (SchoolplanProtocol $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'options' => ['style' => 'width:10px']
                            ],
                            [
                                'attribute' => 'teachers_id',
                                'value' => function (SchoolplanProtocol $model) use ($teachers_list) {
                                    return $teachers_list[$model->teachers_id] ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'filter' => false,
                                'attribute' => 'studyplan_subject_id',
                                'value' => function (SchoolplanProtocol $model) use ($studyplan_subject_list) {
                                    return $studyplan_subject_list[$model->studyplan_subject_id] ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'thematic_items_list',
                                'value' => function ($model) {
                                    if (!empty($model->thematic_items_list[0])) {
                                        $thematic_items_list = StudyplanThematicItems::find()->select('topic')->where(['id' => $model->thematic_items_list])->column();
                                        return implode(', ', $thematic_items_list);
                                    } else {
                                        return $model->task_ticket;
                                    }
                                },
                                'label' => 'Задание/Билет',
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'lesson_mark_id',
                                'value' => function ($model) {
                                    return $model->lessonMark ? $model->lessonMark->mark_label : '';
                                },
                            ],

                            'resume',

                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                            ['/schoolplan/default/protocol', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                            ['/schoolplan/default/protocol', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                                'title' => Yii::t('art', 'View'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                            ['/schoolplan/default/protocol', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'delete'], [
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
                                    'view' => function ($model) use ($model_confirm) {
                                        return \artsoft\Art::isBackend() ? true : in_array($model_confirm->confirm_status, [0, 3]);
                                    },
                                    'update' => function ($model) use ($model_confirm) {
                                        return \artsoft\Art::isBackend() ? true : (($model->isAuthor() || $model->schoolplan->isProtocolSigner()) && in_array($model_confirm->confirm_status, [0, 3]));
                                    },
                                    'delete' => function ($model) use ($model_confirm) {
                                        return \artsoft\Art::isBackend() ? true : (($model->isAuthor() || $model->schoolplan->isProtocolSigner()) && in_array($model_confirm->confirm_status, [0, 3]));
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
    </div>
</div>
