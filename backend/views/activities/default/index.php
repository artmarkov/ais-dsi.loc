<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\activities\Activities;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/calendar', 'Activities');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-index">
    <div class="panel">
        <?= $this->render('_search', compact('model_date')) ?>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => Activities::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'activities-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'activities-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'activities-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        /* 'bulkActionOptions' => [
                             'gridId' => 'activities-grid',
                             'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                         ],*/

                        'columns' => [
                            [
                                'options' => ['style' => 'width:20px'],
                                'contentOptions' => function (Activities $model) {
                                    return ['style' => 'background-color:' . $model->color];
                                },
                            ],
                            ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px']],
                            [
                                'attribute' => 'title',
                                'value' => function (Activities $model) {
                                    return $model->title;
                                },
                            ],
                            [
                                'attribute' => 'category_id',
                                'value' => 'catName',
                                'label' => Yii::t('art/guide', 'Category'),
                                'filter' => \common\models\activities\ActivitiesCat::getCatList(),
                                'options' => ['style' => 'width:150px'],
                            ],
                            [
                                'attribute' => 'auditory_id',
                                'value' => 'auditoryName',
                                'label' => Yii::t('art/guide', 'Name Auditory'),
                                'filter' => \artsoft\helpers\RefBook::find('auditory_memo_1', 1, true)->getList(),
                            ],
//                            'description:ntext',
                            [
                                'attribute' => 'start_time',
                                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                                'value' => function ($model) {
                                    return $model->start_time;
                                },
                                'options' => ['style' => 'width:270px'],
                                'format' => 'raw',
                            ],
                            'end_time',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/activities/default',
                                'template' => '{view}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                            Url::to(['/activities/default/view', 'id' => $model->id, 'resource' => $model->resource]), [
                                                'title' => Yii::t('art', 'View'),
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
    </div>
</div>

<?php
\artsoft\widgets\DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'start_time',
    'format' => 'DD.MM.YYYY H:mm',
    'opens' => 'left',
])
?>

