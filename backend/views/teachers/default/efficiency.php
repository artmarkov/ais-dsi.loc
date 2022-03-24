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

?>
    <div class="teachers-efficiency-index">
        <div class="panel">
            <div class="panel-heading">
                <?= \artsoft\helpers\ButtonHelper::createButton(isset($id) ? ['/teachers/default/efficiency', 'id' => $id,  'mode' => 'create'] : ''); ?>
                <span class="pull-left"> <?= Html::a('<i class="fa fa-bar-chart" aria-hidden="true"></i> График эффективности ', ['/teachers/default/efficiency', 'id' => $id,  'mode' => 'bar'], ['class' => 'btn btn-sm btn-info']); ?></span>
            </div>
            <div class="panel-body">
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
                            'bulkActionOptions' => [
                                'gridId' => 'teachers-efficiency-grid',
                                'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                            ],
                            'columns' => [
                                ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                                [
                                    'attribute' => 'id',
                                    'class' => 'artsoft\grid\columns\TitleActionColumn',
                                    'controller' => '/teachers/efficiency',
                                    'title' => function (TeachersEfficiency $model) {
                                        return Html::a(sprintf('#%06d', $model->id), ['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update'], ['data-pjax' => 0]);
                                    },
                                    'buttonsTemplate' => '{update} {delete}',
                                    'buttons' => [
                                        'update' => function ($key, $model) {
                                            return Html::a(Yii::t('art', 'Update'),
                                                Url::to(['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                                    'title' => Yii::t('art', 'Edit'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                ]
                                            );
                                        },
                                        'delete' => function ($key, $model) {
                                            return Html::a(Yii::t('art', 'Delete'),
                                                Url::to(['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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
                                [
                                    'attribute' => 'efficiency_id',
                                    'value' => 'efficiencyName',
                                    'options' => ['style' => 'width:350px'],
                                    'label' => Yii::t('art/guide', 'Efficiency'),
                                    'filter' => \common\models\efficiency\EfficiencyTree::getEfficiencyList(),
                                ],
//                                [
//                                    'attribute' => 'teachers_id',
//                                    'value' => 'teachersName',
//                                    'label' => Yii::t('art/teachers', 'Teachers'),
//                                    'filter' => \artsoft\helpers\RefBook::find('teachers_fullname')->getList(),
//                                ],
                                [
                                    'attribute' => 'bonus',
                                    'value' => function (TeachersEfficiency $model) {
                                        return $model->bonus . '%';
                                    },
                                ],
                                [
                                    'attribute' => 'date_in',
                                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                                    'value' => function ($model) {
                                        return $model->date_in;
                                    },
                                    'options' => ['style' => 'width:150px'],
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