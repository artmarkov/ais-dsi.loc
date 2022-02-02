<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schedule\ConsultSchedule;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\ConsultScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Consult Schedules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consult-schedule-index">
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
                                'model' => ConsultSchedule::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?=  GridPageSize::widget(['pjaxId' => 'consult-schedule-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'consult-schedule-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'consult-schedule-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'consult-schedule-grid',
                            'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/consult-schedule/default',
                                'title' => function(ConsultSchedule $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

            'id',
            'teachers_load_id',
            'datetime_in:datetime',
            'datetime_out:datetime',
            'auditory_id',
            // 'description',
            // 'created_at',
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'version',

                ],
            ]);
            ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


