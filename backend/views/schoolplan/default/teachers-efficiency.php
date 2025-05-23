<?php

use artsoft\widgets\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\efficiency\TeachersEfficiency;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\efficiency\search\TeachersEfficiencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $id */

?>
    <div class="teachers-efficiency-index">
        <div class="panel">
            <div class="panel-heading">
                <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
            </div>
            <div class="panel-body">
                <?php $model = \common\models\schoolplan\Schoolplan::findOne($id) ?>
                <?php if ($model): ?>
                    <div class="panel">
                        <div class="panel-heading">
                            Показатели эффективности в рамках мероприятия
                        </div>
                        <div class="panel-body">
                            <?= \yii\widgets\DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'title',
                                    'datetime_in',
                                    'datetime_out',
                                ],
                            ]) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <?php
                                /* Uncomment this to activate GridQuickLinks */
                                /* echo GridQuickLinks::widget([
                                    'model' => TeachersEfficiency::className(),
                                    'searchModel' => $searchModel,
                                ])*/
                                ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?= GridPageSize::widget(['pjaxId' => 'teachers-efficiency-grid-pjax']) ?>
                            </div>
                        </div>

                        <?php
                        Pjax::begin([
                            'id' => 'teachers-efficiency-grid-pjax',
                        ])
                        ?>
                        <?= GridView::widget([
                            'id' => 'teachers-efficiency-grid',
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
//                            'bulkActionOptions' => [
//                                'gridId' => 'teachers-efficiency-grid',
//                                'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
//                            ],
                            'columns' => [
//                                ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                                [
                                    'attribute' => 'id',
                                    'value' => function (TeachersEfficiency $model) {
                                        return sprintf('#%06d', $model->id);
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => function (TeachersEfficiency $model) {
                                        return [];
                                    },
                                ],
                                [
                                    'attribute' => 'efficiency_id',
                                    'value' => 'efficiencyName',
                                    'options' => ['style' => 'width:350px'],
                                    'label' => Yii::t('art/guide', 'Efficiency'),
                                    'filter' => \common\models\efficiency\EfficiencyTree::getEfficiencyList(),
                                ],
                                [
                                    'attribute' => 'teachers_id',
                                    'value' => 'teachersName',
                                    'label' => Yii::t('art/teachers', 'Teachers'),
                                    'filter' => \artsoft\helpers\RefBook::find('teachers_fullname')->getList(),
                                ],
                                [
                                    'attribute' => 'bonus',
                                    'value' => function (TeachersEfficiency $model) {
                                        return $model->bonus;
                                    },
                                ],
                                [
                                    'attribute' => 'bonus_vid_id',
                                    'value' => function (TeachersEfficiency $model) {
                                        return \common\models\efficiency\EfficiencyTree::getBobusVidValue('short', $model->bonus_vid_id);
                                    },
                                    'filter' => \common\models\efficiency\EfficiencyTree::getBobusVidList('short'),
                                ],
                                [
                                    'attribute' => 'date_in',
                                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                                    'value' => function ($model) {
                                        return $model->date_in;
                                    },
                                    'options' => ['style' => 'width:150px'],
                                ],
                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'controller' => '/schoolplan/default',
                                    'template' => '{view} {update} {delete}',
                                    'buttons' => [
                                        'update' => function ($url, $model, $key) use ($id) {
                                            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                                ['/schoolplan/default/teachers-efficiency', 'id' => $id, 'objectId' => $model->id, 'mode' => 'update'], [
                                                    'title' => Yii::t('art', 'Edit'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                ]
                                            );
                                        },
                                        'view' => function ($url, $model, $key) use ($id) {
                                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                                ['/schoolplan/default/teachers-efficiency', 'id' => $id, 'objectId' => $model->id, 'mode' => 'view'], [
                                                    'title' => Yii::t('art', 'View'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                ]
                                            );
                                        },
                                        'delete' => function ($url, $model, $key) use ($id) {
                                            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                                ['/schoolplan/default/teachers-efficiency', 'id' => $id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                                    'title' => Yii::t('art', 'Delete'),
                                                    'aria-label' => Yii::t('art', 'Delete'),
                                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                ]
                                            );
                                        },
                                    ],
                                    'headerOptions' => ['class' => 'kartik-sheet-style'],
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
if ($searchModel) {
    DateRangePicker::widget([
        'model' => $searchModel,
        'attribute' => 'date_in',
        'format' => 'DD.MM.YYYY',
        'opens' => 'left',
    ]);
}
?>