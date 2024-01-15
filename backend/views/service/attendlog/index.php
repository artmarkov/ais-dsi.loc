<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\service\UsersAttendlogView;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\service\search\UsersAttendlogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Users Attendlogs');
$this->params['breadcrumbs'][] = $this->title;
$auditory_list = RefBook::find('auditory_memo_1')->getList();

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
            return Html::a($model->user_name,
                ['/service/attendlog/update', 'id' => $model->users_attendlog_id], [
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
        'filter' => $auditory_list,
        'options' => ['style' => 'width:300px'],
        'value' => function ($model) use ($auditory_list) {
            return $auditory_list[$model->auditory_id];
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
            return $model->timestamp_over ? Yii::$app->formatter->asDatetime($model->timestamp_over) : (
            $model->key_free_flag ? Html::a('<i class="fa fa-male" aria-hidden="true"></i> Завершить работу',
                ['/service/attendlog/over', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-info',
                    'title' => 'Завершить работу',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]) :
                Html::a('<i class="fa fa-key" aria-hidden="true"></i> Вернуть ключ',
                    ['/service/attendlog/over', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-success',
                        'title' => 'Вернуть ключ',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ])
            );
        },
        'format' => 'raw',
    ],
    'comment:text',

];
?>
<div class="users-attendlog-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Document::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'users-attendlog-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'users-attendlog-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
                'id' => 'users-attendlog-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Пользователь', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'Ключи от аудиторий', 'options' => ['colspan' => 4, 'class' => 'text-center info']],
                        ],
                    ]
                ],
            ]);
            ?>
            <?php Pjax::end() ?>

        </div>
    </div>
</div>


