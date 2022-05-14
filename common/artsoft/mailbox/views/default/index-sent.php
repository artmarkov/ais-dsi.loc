<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use artsoft\mailbox\models\Mailbox;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use yii\helpers\ArrayHelper;
use yii\timeago\TimeAgo;

/* @var $this yii\web\View */
/* @var $searchModel artsoft\mailbox\models\search\MailboxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/mailbox', 'Sent');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mailbox-sent-index">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <?= Html::a(Yii::t('art/mailbox', 'Compose'), ['/mailbox/default/compose'], ['class' => 'btn btn-primary btn-block margin-bottom']) ?>

                    <div class="panel panel-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= Yii::t('art/mailbox', 'Folders'); ?></h3>
                        </div>

                        <div class="box-body no-padding">

                            <?= $this->render('_menu') ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php
                                    /* Uncomment this to activate GridQuickLinks */
                                    /*  echo GridQuickLinks::widget([
                                        'model' => Mailbox::className(),
                                        'searchModel' => $searchModel,
                                    ]) */
                                    ?>
                                </div>

                                <div class="col-sm-6 text-right">
                                    <?= GridPageSize::widget(['pjaxId' => 'mailbox-sent-grid-pjax']) ?>
                                </div>
                            </div>
                            <?php
                            Pjax::begin([
                                'id' => 'mailbox-sent-grid-pjax',
                            ])
                            ?>

                            <?=
                            GridView::widget([
                                'id' => 'mailbox-sent-grid',
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'toolbar' => false,
                                'bulkActionOptions' => [
                                    'gridId' => 'mailbox-sent-grid',
                                    'actions' => [
                                        Url::to(['bulk-trash-sent']) => Yii::t('art/mailbox', 'Move to Trash'),

                                    ] //Configure here you bulk actions
                                ],
                                'columns' => [
                                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                                    [
                                        'attribute' => 'gridReceiverSearch',
                                        'filter' => artsoft\models\User::getUsersList(),
                                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                                        'controller' => '/mailbox/default',
                                        'title' => function (Mailbox $model) {
                                            return implode(', ',
                                                ArrayHelper::map($model->receivers, 'id', 'username'));
                                        },
                                        'options' => ['style' => 'width:350px'],
                                        'format' => 'raw',
                                        'buttonsTemplate' => '{view} {trash}',
                                        'buttons' => [
                                            'view' => function ($url, $model, $key) {
                                                return Html::a(Yii::t('yii', 'View'), ['/mailbox/default/view-sent', 'id' => $model->id], [
                                                        'title' => Yii::t('yii', 'View'),
                                                        'data-pjax' => '0'
                                                    ]
                                                );
                                            },
                                            'trash' => function ($url, $model, $key) {
                                                return Html::a(Yii::t('art/mailbox', 'Move to Trash'), ['/mailbox/default/trash-sent', 'id' => $model->id], [
                                                        'title' => Yii::t('art/mailbox', 'Move to Trash'),
                                                        'data-pjax' => '0'
                                                    ]
                                                );
                                            }
                                        ],
                                    ],

                                    'title',
//                    [
//                        'attribute' => 'content',
//                        'value' => 'shortContent',
//                        'format' => 'html',
//                    ],
                                    [
                                        'value' => function ($model) {
                                            return $model->clip;
                                        },
                                        'format' => 'html',
                                    ],
                                    [
                                        'class' => 'artsoft\grid\columns\DateRangeFilterColumn',
                                        'attribute' => 'dateSearch_1',
                                        'attribute2' => 'dateSearch_2',
                                        'value' => function ($model) {
                                            return $model->createdDatetime . '<br />(' . TimeAgo::widget(
                                                    [
                                                        'timestamp' => $model->created_at,
                                                        'language' => Yii::$app->art->getDisplayLanguageShortcode(Yii::$app->language)
                                                    ]) . ')';
                                        },
                                        'label' => Yii::t('art', 'Created'),
                                        'format' => 'raw',
                                        'options' => ['style' => 'width:230px'],
                                    ],
//                    [
//                        'attribute' => 'sender_id',
//                        'label' => Yii::t('art/mailbox', 'Sender'),
//                        'value' => function (Mailbox  $model) {
//                                        return $model->senderName;
//                                    },
//                        'filter' =>  artsoft\models\User::getUsersList(),
//                    ],
                                    // 'created_at',
                                    // 'updated_at',
                                    // 'deleted_at',

                                ],
                            ]);
                            ?>

                            <?php Pjax::end() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
