<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\guidejob\Bonus;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\guidejob\search\BonusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Teachers Bonus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonus-index">
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
                                'model' => Bonus::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'bonus-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'bonus-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'bonus-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'bonus-grid',
                            //'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Bonus $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'value' => function (Bonus $model) {
                                    return $model->name;
                                },
                            ],
                            'slug',
                            [
                                'attribute' => 'bonus_category_id',
                                'value' => 'bonusCategoryName',
                                'label' => Yii::t('art/teachers', 'Bonus Category'),
                                'filter' => common\models\guidejob\BonusCategory::getBonusCategoryList(),
                            ],
                            'value_default',
                            [
                                'attribute' => 'bonus_vid_id',
                                'value' => function (Bonus $model) {
                                    return \common\models\efficiency\EfficiencyTree::getBobusVidValue('short', $model->bonus_vid_id);
                                },
                                'filter' => \common\models\efficiency\EfficiencyTree::getBobusVidList('short'),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Bonus::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [Bonus::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:60px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '//guidejob/bonus',
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


