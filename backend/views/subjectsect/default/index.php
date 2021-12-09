<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subjectsect\SubjectSect;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectSectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Sects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-index">
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
                                'model' => SubjectSect::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'subject-sect-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-sect-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'subject-sect-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'subject-sect-grid',
                            'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/subjectsect/default',
                                'title' => function (SubjectSect $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'subject_cat_id',
                                'filter' => RefBook::find('subject_category_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_category_name')->getValue($model->subject_cat_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_id',
                                'filter' => RefBook::find('subject_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_name')->getValue($model->subject_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_type_id',
                                'filter' => RefBook::find('subject_type_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_type_name')->getValue($model->subject_type_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_vid_id',
                                'filter' => RefBook::find('subject_vid_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_vid_name')->getValue($model->subject_vid_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'course',
                                'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                                'value' => function (SubjectSect $model) {
                                    return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                                },
                                'options' => ['style' => 'width:100px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'plan_year',
                                'filter' => \artsoft\helpers\ArtHelper::getStudyYearsList(),
                                'value' => function (SubjectSect $model) {
                                    return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                                },
                                'options' => ['style' => 'width:100px'],
                                'format' => 'raw',
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


