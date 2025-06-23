<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\SchoolplanProtocol;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanProtocolSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Schoolplan Protocol');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schoolplan-protocol-index">
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
                        'model' => SchoolplanProtocol::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'schoolplan-protocol-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'schoolplan-protocol-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'schoolplan-protocol-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'schoolplan-protocol-grid',
                    'actions' => [Url::to(['protocol-bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (SchoolplanProtocol $model) {
                            return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                    ],

                    'schoolplan_id',
                    'protocol_name',
                    'description',
                    'protocol_date',
                    'leader_id',
                    'secretary_id',
                    'members_list',
                    'subject_list',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                        'width' => '90px',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'update' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/schoolplan-protocol/default/update', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/schoolplan-protocol/default/delete', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-qrcode" aria-hidden="true" style="color: red"></span>',
                                    ['/schoolplan-protocol/default/view', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            }
                        ],
                       /* 'visibleButtons' => [
                            'delete' => function ($model) {
                                return $model->studyplan_invoices_id !== null;
                            },
                            'update' => function ($model) {
                                return $model->studyplan_invoices_id !== null;
                            },
                            'view' => function ($model) {
                                return $model->studyplan_invoices_id !== null;
                            }
                        ],*/
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


