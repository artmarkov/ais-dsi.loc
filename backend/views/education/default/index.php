<?php

use common\models\studyplan\StudyplanView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\education\EducationProgramm;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\EducationProgrammSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Education Programms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-programm-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <?php echo \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info-circle"></i> Этим цветом отмечены программы с дополнительным годом обучения',
                'options' => ['class' => 'alert-success'],
            ]);
            echo \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info-circle"></i> Этим цветом отмечены программы, в которых срок обучения не соответствует кол-ву этапов учебного плана',
                'options' => ['class' => 'alert-warning'],
            ]);
            ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            echo \artsoft\grid\GridQuickLinks::widget([
                                'model' => EducationProgramm::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'education-programm-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'education-programm-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'education-programm-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'education-programm-grid',
                            'actions' => [
                                Url::to(['bulk-activate']) => Yii::t('art', 'Activate'),
                                Url::to(['bulk-deactivate']) => Yii::t('art', 'Deactivate'),
                                Url::to(['bulk-delete']) => Yii::t('art', 'Delete'),
                            ] //Configure here you bulk actions
                        ],
                        'rowOptions' => function (EducationProgramm $model) {
                            if (count($model->programmLevel) == $model->term_mastering + 1) {
                                return ['class' => 'success'];
                            } elseif (count($model->programmLevel) != $model->term_mastering) {
                                return ['class' => 'warning'];
                            }
                            return [];
                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (EducationProgramm $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'contentOptions' => function (EducationProgramm $model) {
                                    return [];
                                },
                            ],
//                            [
//                                'attribute' => 'id',
//                                'class' => 'artsoft\grid\columns\TitleActionColumn',
//                                'controller' => '/education/default',
//                                'title' => function (EducationProgramm $model) {
//                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
//                                },
//                                'buttonsTemplate' => '{update} {view} {delete}',
//                            ],
                            [
                                'attribute' => 'education_cat_id',
                                'filter' => RefBook::find('education_cat_short')->getList(),
                                'value' => function (EducationProgramm $model) {
                                    return RefBook::find('education_cat_short')->getValue($model->education_cat_id);
                                },
                            ],
                            [
                                'attribute' => 'term_mastering',
                                'filter' => \artsoft\helpers\ArtHelper::getTermList(),
                                'value' => function (EducationProgramm $model) {
                                    return \artsoft\helpers\ArtHelper::getTermList()[$model->term_mastering];
                                },
                            ],

                            'name',
                            'short_name',
//                            'description',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [EducationProgramm::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [EducationProgramm::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/education/default',
                                'template' => '{view} {update} {delete}',
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


