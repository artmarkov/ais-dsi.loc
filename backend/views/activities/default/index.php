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
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
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
                            <?=  GridPageSize::widget(['pjaxId' => 'activities-grid-pjax']) ?>
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
                        'bulkActionOptions' => [
                            'gridId' => 'activities-grid',
                            'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'title',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/activities/default',
                                'title' => function(Activities $model) {
                                    return Html::a($model->title, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'category_id',
                                'value' => 'catName',
                                'label' => Yii::t('art/guide', 'Category'),
                                'filter' => \common\models\activities\ActivitiesCat::getCatList(),
                            ],
                            [
                                'attribute' => 'auditory_id',
                                'value' => 'auditoryName',
                                'label' => Yii::t('art/guide', 'Name Auditory'),
                                'filter' => \artsoft\helpers\RefBook::find('auditory_memo_1', 1, true)->getList(),
                            ],
                            'description:ntext',
                            [
                                'attribute' => 'start_time',
                                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                                'value' => function ($model)  {
                                        return $model->start_time;
                                },
                                'options' => ['style' => 'width:270px'],
                                'format' => 'raw',
                            ],
                            'end_time',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'all_day',
                                'options' => ['style' => 'width:60px']
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

