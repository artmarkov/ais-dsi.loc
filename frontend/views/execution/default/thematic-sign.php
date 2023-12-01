<?php

use artsoft\helpers\RefBook;
use common\models\schoolplan\Schoolplan;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\studyplan\StudyplanThematic;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\ThematicViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$teachers_list = RefBook::find('teachers_fio')->getList();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'subject',
        'value' => function ($model) {
            return $model->subject;
        },
        'group' => true,
    ],
    [
        'attribute' => 'sect_name',
        'width' => '320px',
        'value' => function ($model) {
            return $model->sect_name ? $model->sect_name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'format' => 'raw',
    ],
    [
        'attribute' => 'thematic_category',
        'value' => function ($model) {
            return StudyplanThematic::getCategoryValue($model->thematic_category);
        },
        'format' => 'raw',
//        'group' => true,
//        'subGroupOf' => 1
    ],
    [
        'attribute' => 'half_year',
        'value' => function (StudyplanThematic $model) {
            return \artsoft\helpers\ArtHelper::getHalfYearValue($model->half_year);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) use ($teachers_list) {
            $teachers_fio = $teachers_list[$model->teachers_id] ?? $model->teachers_id;
            return $teachers_fio;
        },
        'format' => 'raw',
//        'group' => true,  // enable grouping
//        'subGroupOf' => 2
    ],
    [
        'attribute' => 'doc_sign_teachers_id',
        'filter' => RefBook::find('teachers_fio')->getList(),
        'value' => function (StudyplanThematic $model) {
            return RefBook::find('teachers_fio')->getValue($model->doc_sign_teachers_id);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'doc_status',
        'optionsArray' => [
            [StudyplanThematic::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
            [StudyplanThematic::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
            [StudyplanThematic::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
        ],
        'options' => ['style' => 'width:150px']
    ],
    [
        'attribute' => 'doc_sign_timestamp',
        'value' => function (StudyplanThematic $model) {
            return Yii::$app->formatter->asDatetime($model->doc_sign_timestamp);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'visible' => \artsoft\Art::isFrontend(),
        'width' => '90px',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                    ['/execution/teachers/thematic-items', 'id' => $model->teachers_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'view'], [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
        'visibleButtons' => [
            'view' => function ($model) {
                return $model->studyplan_thematic_id;
            }
        ],
    ],
];
?>
<div class="teachers-thematic-index">
    <div class="panel">
        <div class="panel-heading">
            Тематические планы на подписи: <?php echo RefBook::find('teachers_fio')->getValue($model->id); ?>
        </div>
        <div class="panel-body">
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-thematic-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-thematic-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'studyplan-thematic-grid',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'План', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Подпись', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

