<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\service\UsersAttendlogView;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\service\search\UsersAttendlogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Users Attendlogs');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'options' => ['style' => 'width:30px'],
        'attribute' => 'id',
        'label' => Yii::t('art', 'ID'),
        'value' => function (UsersAttendlogView $model) {
            return sprintf('#%06d', $model->id);
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'user_name',
        'value' => function (UsersAttendlogView $model) {
          return  Html::a($model->user_name,
                Url::to(['/service/attendlog/update', 'id' => $model->users_attendlog_id]), [
                    'title' => Yii::t('art', 'Edit'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]
            );
            },
        'format' => 'raw',
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'user_category',
        'filter' => \common\models\user\UserCommon::getUserCategoryList(),
        'value' => function (UsersAttendlogView $model) {
            return $model->user_category_name;
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'auditory_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => RefBook::find('auditory_memo_1')->getList(),
        'options' => ['style' => 'width:300px'],
        'value' => function ($model) {
            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    'timestamp_received:datetime',
    [
        'attribute' => 'timestamp_over',
        'value' => function (UsersAttendlogView $model) {
            return $model->timestamp_over ? Yii::$app->formatter->asDate($model->timestamp_over, 'php:d.m.Y H:i') : Html::a('<i class="fa fa-key" aria-hidden="true"></i> Вернуть ключ',
                Url::to(['/service/attendlog/over', 'id' => $model->id]), [
                    'class' => 'btn btn-sm btn-warning btn-block',
                    'title' => 'Сдать ключ',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]
            );
        },
        'format' => 'raw',
    ],

];
?>
<div class="users-attendlog-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>

            <?php
            Pjax::begin([
                'id' => 'users-attendlog-grid-pjax',
            ])
            ?>

            <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table-condensed'],
                        //                        'showPageSummary' => true,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => 'Журнал выдачи ключей',
                            'type' => 'default',
                            'after' => '',
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                        'columns' => $columns,
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Пользователь', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                                    ['content' => 'Ключи от аудиторий', 'options' => ['colspan' => 4, 'class' => 'text-center info']],
                                ],
                                'options' => ['class' => 'skip-export'] // remove this row from export
                            ]
                        ],
                        'exportConfig' => [
                            'html' => [],
                            'csv' => [],
                            'txt' => [],
                            'xls' => [],
                        ],
                        'toolbar' => [
                            [
                                'content' => Html::a('Очистить',
                                    Url::to(['/service/attendlog']), [
                                        'title' => 'Очистить',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-default'
                                    ]
                                ),
                            ],
                            '{export}',
                            '{toggleData}'
                        ],
                    ]);
                    ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


