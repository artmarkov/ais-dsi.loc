<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\StudyplanView;
use artsoft\grid\GridPageSize;
use common\models\studyplan\Studyplan;

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
            <?= $this->render('_search', compact('model_date', 'teachers_id')) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                         'model' => StudyplanView::className(),
                         'searchModel' => $searchModel,
                     ])*/
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
                'pjax' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => \artsoft\Art::isFrontend() && User::hasRole(['student']) ? false : $searchModel,
                'bulkActionOptions' =>  \artsoft\Art::isBackend() ? [
                    'gridId' => 'studyplan-grid',
                    'actions' =>  [
                        Url::to(['bulk-next-class']) => 'Перевести в следующий класс',
                        Url::to(['bulk-repeat-class']) => 'Повторить учебную программу',
                        Url::to(['bulk-finish-plan']) => 'Завершить учебную программу',
                        /*Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),*/
                    ]
                ] : false,
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn',  'visible' => \artsoft\Art::isBackend(), 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (Studyplan $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'contentOptions' => function (Studyplan $model) {
                            return [];
                        },
                        'options' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'studentFio',
                        'filter' => RefBook::find('students_fullname')->getList(),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (Studyplan $model) {
                            return $model->studentFio;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'value' => function (Studyplan $model) {
                            return $model->studentPhone;
                        },
                        'label' =>  'Телефон'
                    ],
                    [
                        'attribute' => 'programmName',
                        'filter' => \common\models\education\EducationProgramm::getProgrammList(),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (Studyplan $model) {
                            return $model->programmName;
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'subject_form_id',
                        'filter' => RefBook::find('subject_form_name')->getList(),
                        'value' => function (Studyplan $model) {
                            return $model->getSubjectFormName();
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'course',
                        'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'value' => function (Studyplan $model) {
                            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'value' => function (Studyplan $model) {
                            return $model->getSpeciality();
                        },
                        'label' => 'Специальность'

],
                    /*[
                        'attribute' => 'plan_year',
                        'filter' => false,
                        'value' => function (Studyplan $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],*/
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Studyplan::STATUS_ACTIVE, 'План открыт', 'info'],
                            [Studyplan::STATUS_INACTIVE, 'План закрыт', 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/studyplan/default',
                        'template' => \artsoft\Art::isBackend() ? '{view} {update} {delete}' : '{view}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


