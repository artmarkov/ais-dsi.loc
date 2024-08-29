<?php

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Teachers Plan');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indivplan-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
                    <?php

                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => TeachersPlan::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'indivplan-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'indivplan-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'indivplan-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'teachers-plan-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (TeachersPlan $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],

                    [
                        'attribute' => 'direction_id',
                        'filter' => \common\models\guidejob\Direction::getDirectionList(),
                        'value' => function ($model, $key, $index, $widget) {
                            return $model->direction ? $model->direction->name : null;
                        },

                    ],
                    [
                        'attribute' => 'teachers_id',
                        'filterType' => GridView::FILTER_SELECT2,
                        'width' => '250px',
                        'filter' => RefBook::find('teachers_fullname', \common\models\user\UserCommon::STATUS_ACTIVE)->getList(),
                        'value' => function ($model) {
                            return $model->teachers->fullName;
                        },
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                    ],
                    [
                        'attribute' => 'plan_year',
                        'filter' => false,
                        'value' => function (TeachersPlan $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsValue($model->plan_year);
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'half_year',
                        'filter' => \artsoft\helpers\ArtHelper::getHalfYearList(),
                        'value' => function (TeachersPlan $model) {
                            return \artsoft\helpers\ArtHelper::getHalfYearValue($model->half_year);
                        },
                        'options' => ['style' => 'width:150px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'week_num',
                        'filter' => ArtHelper::getWeekList(),
                        'value' => function (TeachersPlan $model) {
                            return ArtHelper::getWeekValue('name', $model->week_num);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'week_day',
                        'filter' => ArtHelper::getWeekDayList('name', 1, 6),
                        'value' => function (TeachersPlan $model) {
                            return ArtHelper::getWeekdayValue('name', $model->week_day);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'planDisplay',
                        'value' => function (TeachersPlan $model) {
                            return $model->getPlanTimeDisplay();
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'auditory_id',
                        'filter' => RefBook::find('auditory_memo_1')->getList(),
                        'value' => function ($model) {
                            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                        },
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                        'width' => '90px',
                        'controller' => '/indivplan/default',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'template' => '{view} {update} {delete}',
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


