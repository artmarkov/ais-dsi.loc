<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\EntrantProgramm;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\searchEntrantProgrammSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Entrant Programms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-programm-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EntrantProgramm::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-programm-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-programm-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-programm-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-programm-grid',
                    'actions' => [
                        Url::to(['bulk-activate']) => Yii::t('art', 'Activate'),
                        Url::to(['bulk-deactivate']) => Yii::t('art', 'Deactivate'),
                        Url::to(['bulk-delete']) => Yii::t('art', 'Delete'), //Configure here you bulk actions
                    ],
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (EntrantProgramm $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'contentOptions' => function (EntrantProgramm $model) {
                            return [];
                        },
                    ],
                    [
                        'attribute' => 'programm_id',
                        'filter' => RefBook::find('education_programm_short_name')->getList(),
                        'value' => function (EntrantProgramm $model) {
                            return RefBook::find('education_programm_short_name')->getValue($model->programm_id);
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    'name',
                    [
                        'attribute' => 'subject_type_id',
                        'filter' => RefBook::find('subject_type_name')->getList(),
                        'value' => function (EntrantProgramm $model) {
                            return RefBook::find('subject_type_name')->getValue($model->subject_type_id);
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'course',
                        'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'value' => function (EntrantProgramm $model) {
                            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    'age_in',
                    'age_out',
                    'qty_entrant',
                    'qty_reserve',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [EntrantProgramm::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                            [EntrantProgramm::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/education/entrant-programm',
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


