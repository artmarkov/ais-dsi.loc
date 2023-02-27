<?php

use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subjectsect\SubjectSect;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;
use yii\helpers\Url;

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
                                'value' => function (SubjectSect $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'contentOptions' => function (SubjectSect $model) {
                                    if ($model->course_flag == 1) {
                                        return ['class' => 'success'];
                                    }
                                    return ['class' => 'info'];
                                },
                            ],
                            [
                                'attribute' => 'subject_id',
                                'filter' => \common\models\subject\Subject::getSubjectListGroup(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_name')->getValue($model->subject_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'sect_name',
                                'value' => function (SubjectSect $model) {
                                    return $model->sect_name;
                                },
                            ],
                             /*[
                                'attribute' => 'sect_name',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/sect/default',
                                'options' => ['style' => 'width:350px'],
                                'title' => function (SubjectSect $model) {
                                    return Html::a($model->sect_name, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],*/
                            [
                                'attribute' => 'programm_list',
                                'filter' => RefBook::find('education_programm_short_name', \common\models\education\EducationProgramm::STATUS_ACTIVE)->getList(),
                                'value' => function (SubjectSect $model) {
                                    $v = [];
                                    foreach ($model->programm_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('education_programm_short_name', \common\models\education\EducationProgramm::STATUS_ACTIVE)->getValue($id);
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:250px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_cat_id',
                                'filter' => RefBook::find('subject_category_name_dev')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_category_name_dev')->getValue($model->subject_cat_id);
                                },
                                'format' => 'raw',
                                'label' => 'Раздел'
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
                                'filter' => \common\models\subject\SubjectVid::getVidListGroup(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_vid_name')->getValue($model->subject_vid_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'sub_group_qty',
                            [
                                'class' => 'kartik\grid\ActionColumn',
//                                'urlCreator' => function ($action, $model, $key, $index) {
//                                    return [$action, 'id' => $key];
//                                },
                                'controller' => '/sect/default',
                                'template' => '{view} {update} {clone} {delete}',
                                'buttons' => [
                                    'clone' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span>',
                                                Url::to(['/sect/default/create', 'id' => $model->id]), [
                                                'title' => Yii::t('art', 'Clone'),
                                                'data-method' => 'post',
                                                'data-confirm' => Yii::t('art', 'Are you sure you want to clone this item?'),
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                ],
//                                'headerOptions' => ['class' => 'kartik-sheet-style'],
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


