<?php

use artsoft\helpers\RefBook;
use artsoft\queue\models\QueueSchedule;
use common\models\studyplan\Studyplan;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanProtocolItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Schoolplan Protocol Items');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'id',
        'class' => 'artsoft\grid\columns\TitleActionColumn',
        'controller' => '/studyplan/default/protocol-items',
        'title' => function (\common\models\schoolplan\SchoolplanProtocolItems $model) {
            return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
        },
        'buttonsTemplate' => '{update} {view} {delete}',
        'buttons' => [
            'update' => function ($key, $model) {
                return Html::a(Yii::t('art', 'Edit'),
                    Url::to(['/studyplan/default/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'view' => function ($key, $model) {
                return Html::a(Yii::t('art', 'View'),
                    Url::to(['/studyplan/default/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'view']), [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a(Yii::t('art', 'Delete'),
                    Url::to(['/studyplan/default/studyplan-perform', 'id' => $model->studyplan_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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
    'schoolplan_protocol_id',
    [
        'attribute' => 'studyplan_subject_id',
        'filter' => RefBook::find('subject_memo_1')->getList(),
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
        'options' => ['style' => 'width:100px'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'lesson_mark_id',
        'filter' => RefBook::find('lesson_mark')->getList(),
        'value' => function ($model) {
            return RefBook::find('lesson_mark')->getValue($model->lesson_mark_id);
        },
        'options' => ['style' => 'width:100px'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'winner_id',
        'filter' => \common\models\schoolplan\SchoolplanProtocolItems::getWinnerList(),
        'value' => function ($model) {
            return \common\models\schoolplan\SchoolplanProtocolItems::getWinnerValue($model->winner_id);
        },
        'options' => ['style' => 'width:100px'],
        'format' => 'raw',
    ],
    'resume:text',
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'status_exe',
        'optionsArray' => \common\models\schoolplan\SchoolplanProtocolItems::getStatusExeOptionsList(),
        'options' => ['style' => 'width:100px'],
    ],
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'status_sign',
        'optionsArray' => \common\models\schoolplan\SchoolplanProtocolItems::getStatusSignOptionsList(),
        'options' => ['style' => 'width:100px'],
    ],
];
?>
<div class="subject-protocol-index">
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
                        'model' => SubjectSect::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-protocol-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'subject-protocol-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'subject-protocol-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table-condensed'],
                'columns' => $columns,
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

