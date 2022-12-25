<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\SortableGridView;
use artsoft\grid\GridQuickLinks;
use common\models\auditory\Auditory;
use common\models\auditory\AuditoryCat;
use common\models\auditory\AuditoryBuilding;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\auditory\search\AuditorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Auditory');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditory-index">
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
                            echo GridQuickLinks::widget([
                                'model' => Auditory::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'auditory-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'auditory-grid-pjax',
                    ])
                    ?>

                    <?=
                    SortableGridView::widget([
                        'id' => 'auditory-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'auditory-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'id',
                                'controller' => '/auditory/default',
                                'title' => function (Auditory $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['/auditory/default/update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],

                            'num',
                            'name',
                            //'catName',
                            //'buildingName',
                            [
                                'attribute' => 'cat_id',
                                'value' => 'catName',
                                'label' => Yii::t('art/guide', 'Name Auditory Category'),
                                'filter' => AuditoryCat::getAuditoryCatList(),
                            ],
                            [
                                'attribute' => 'building_id',
                                'value' => 'buildingName',
                                'label' => Yii::t('art/guide', 'Name Building'),
                                'filter' => AuditoryBuilding::getAuditoryBuildingList(),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Auditory::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [Auditory::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
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


