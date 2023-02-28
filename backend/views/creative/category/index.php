<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\creative\CreativeCategory;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/creative', 'Creative Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative', 'Creative Works'), 'url' => ['creative/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="creative-category-index">
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
                                'model' => CreativeCategory::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'creative-category-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'creative-category-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'creative-category-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'creative-category-grid',
                            'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (CreativeCategory $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'value' => function (CreativeCategory $model) {
                                    return $model->name;
                                },
                            ],
                            'description:ntext',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/creative/category',
                                'template' => '{update} {delete}',
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


