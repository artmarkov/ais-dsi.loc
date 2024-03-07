<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\info\Board;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */
/* @var $modelTeachers */

$this->title = Yii::t('art/guide', 'Portfolio');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'title',
        'value' => function ($model) {
            return Yii::$app->formatter->asDatetime($model->datetime_in) . ' - ' . Yii::$app->formatter->asDatetime($model->datetime_out) . '<br/>' . $model->title;
        },
        'width' => '300px',
        'format' => 'raw',
        'group' => true,
    ],
    [
        'attribute' => 'subject',
        'value' => function ($model) {
            return $model->studyplan_subject_id ? $model->subject : '';
        },
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'sect_name',
        'value' => function ($model) {
            return $model->studyplan_id ? $model->sect_name : '';
        },
        'label' => Yii::t('art/student', 'Student'),
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
    ],
  /*  [
        'attribute' => 'teachers_id',
        'value' => function ($model) use ($teachers_list) {
            return $teachers_list[$model->teachers_id] ?? '';
        },
    ],*/
    [
        'attribute' => 'resume',
        'value' => function ($model) {
            return $model->resume;
        },
    ],
    [
        'attribute' => 'mark_label',
        'value' => function ($model) {
            return $model->mark_label;
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'winner_id',
        'value' => function ($model) {
            return $model->getWinnerValue($model->winner_id);
        },
    ],
//    [
//        'label' => 'Файлы',
//        'value' => function (\common\models\teachers\PortfolioView $model) {
//            return artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'id' => $model->schoolplan_perform_id, 'pluginOptions' => [
//                'showCaption' => false,
//                'showBrowse' => false,
//                'showUpload' => false,
//                'dropZoneEnabled' => false,
//                'fileActionSettings' => [
//                    'showDrag' => false,
//                    'showRemove' => false,
//                ],
//            ],]);
//
//        },
//        'format' => 'html',
//    ],
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'status_exe',
        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusExeOptionsList(),
        'options' => ['style' => 'width:100px'],
    ],
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'status_sign',
        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusSignOptionsList(),
        'options' => ['style' => 'width:100px'],
        'visible' => Yii::$app->settings->get('mailing.schoolplan_perform_doc')
    ],
    [
        'attribute' => 'signer_id',
        'value' => function (\common\models\teachers\PortfolioView $model) {
            return $model->user->userCommon ? $model->user->userCommon->lastFM : $model->signer_id;
        },
        'options' => ['style' => 'width:150px'],
        'contentOptions' => ['style'=>"text-align:center; vertical-align: middle;"],
        'format' => 'raw',
        'visible' => Yii::$app->settings->get('mailing.schoolplan_perform_doc')
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                    ['/schoolplan/default/perform', 'id' => $model->schoolplan_id, 'objectId' => $model->schoolplan_perform_id, 'mode' => 'view'], [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'target' => '_blank',
                    ]
                );
            },

        ],
    ],


];
?>
<div class="portfolio-index">
    <div class="panel">
        <div class="panel-heading">
            Портфолио преподавателя: <?php echo RefBook::find('teachers_fullname')->getValue($id); ?>
        </div>
        <div class="panel-body">
            <?php echo \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info-circle"></i> Данная таблица формируется за весь учебный год и отражает всю работу преподавателя согласно плану работы и индивидуальных планов учящихся. ',
                'options' => ['class' => 'alert-info'],
            ]);
            ?>
            <?= $this->render('_search', compact('model_date')) ?>
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
                    <?php /*\artsoft\grid\GridPageSize::widget(['pjaxId' => 'portfolio-grid-pjax']) */ ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'portfolio-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'portfolio-grid',
                'pjax' => false,
                'dataProvider' => $dataProvider,
                'columns' => $columns,
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

