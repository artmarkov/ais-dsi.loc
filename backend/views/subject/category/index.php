<?php

use yii\widgets\Pjax;
use artsoft\grid\SortableGridView;
use artsoft\grid\GridQuickLinks;
use common\models\subject\SubjectCategory;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subjects'), 'url' => ['subject/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-category-item-index">
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
                              'model' => SubjectCategory::className(),
                              'searchModel' => $searchModel,
                              ]) */
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?php
                            /*= GridPageSize::widget(['pjaxId' => 'subject-category-item-grid-pjax'])*/
                            ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-category-item-grid-pjax',
                    ])
                    ?>

                    <?=
                    SortableGridView::widget([
                        'id' => 'subject-category-item-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'subject-category-item-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (SubjectCategory $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            'name',
                            'slug',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'dep_flag',
                                'optionsArray' => [
                                    [1, Yii::t('art', 'Yes'), 'success'],
                                    [0, Yii::t('art', 'No'), 'danger'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'frequency',
                                'optionsArray' => [
                                    [SubjectCategory::WEEKLY, Yii::t('art/guide', 'Weekly'), 'primary'],
                                    [SubjectCategory::MONTHLY, Yii::t('art/guide', 'Monthly'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [SubjectCategory::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [SubjectCategory::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/subject/category',
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


