<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\StudyplanView;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Individual plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                     echo GridQuickLinks::widget([
                         'model' => StudyplanView::className(),
                         'searchModel' => $searchModel,
                     ])
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'studyplan-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'studyplan-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'studyplan-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (StudyplanView $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'contentOptions' => function (StudyplanView $model) {
                            return [];
                        },
                    ],
                    'student_fio',
                    'education_programm_name',
                    [
                        'attribute' => 'subject_form_id',
                        'filter' => RefBook::find('subject_form_name')->getList(),
                        'value' => function (StudyplanView $model) {
                            return $model->subject_form_name;
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'course',
                        'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'value' => function (StudyplanView $model) {
                            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'plan_year',
                        'filter' => false,
                        'value' => function (StudyplanView $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [StudyplanView::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                            [StudyplanView::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/studyplan/default',
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


