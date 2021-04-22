<?php

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

$this->title = Yii::t('art/guide', 'Efficiencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-efficiency-index">
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
                                'controller' => '/efficiency/default',
                                'title' => function (TeachersEfficiency $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
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
                                'filter' => \common\models\teachers\Teachers::getTeachersList(),
                            ],
                            [
                                'attribute' => 'bonus',
                                'value' => function (TeachersEfficiency $model) {
                                    return $model->bonus . '%';
                                },
                            ],
                            'date_in',
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
