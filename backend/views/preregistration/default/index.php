<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\EntrantPreregistrations;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\EntrantPreregistrationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Entrant Preregistrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-preregistrations-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
            <?= $this->render('_search', compact('model_date')) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EntrantPreregistrations::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-preregistrations-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-preregistrations-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-preregistrations-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-preregistrations-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (EntrantPreregistrations $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'options' => ['style' => 'width:100px']
                    ],
                    [
                        'attribute' => 'entrant_programm_id',
                        'filter' => \common\models\education\EntrantProgramm::getEntrantProgrammList(false),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (EntrantPreregistrations $model) {
                            return \common\models\education\EntrantProgramm::getEntrantProgrammValue($model->entrant_programm_id, false) ?? '';
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                        'group' => true,
                    ],
                    [
                        'attribute' => 'student_id',
                        'filter' => EntrantPreregistrations::getEntrantPreregistrationList(),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (EntrantPreregistrations $model) {
                            return RefBook::find('students_fullname')->getValue($model->student_id);
                        },
                        'format' => 'raw'
                    ],

                    /*[
                        'attribute' => 'plan_year',
                        'filter' => false,
                        'value' => function (EntrantPreregistrations $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'format' => 'raw',
                    ],*/
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'reg_vid',
                        'optionsArray' => [
                            [EntrantPreregistrations::REG_ENTRANT, Yii::t('art/guide', 'Reg Entrant'), 'info'],
                            [EntrantPreregistrations::REG_RESERVE, Yii::t('art/guide', 'Reg Reserve'), 'warning'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [EntrantPreregistrations::REG_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
                            [EntrantPreregistrations::REG_STATUS_STUDENT, Yii::t('art/guide', 'Accepted for training'), 'success'],
                            [EntrantPreregistrations::REG_STATUS_OUTSIDE, Yii::t('art/guide', 'Refused admission'), 'danger'],
                            [EntrantPreregistrations::REG_PLAN_CLOSED, Yii::t('art/guide', 'Plan closed'), 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/preregistration/default',
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


