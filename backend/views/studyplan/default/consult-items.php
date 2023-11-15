<?php
use artsoft\helpers\NoticeConsultDisplay;
use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Consult Schedule');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();
$auditory_list = RefBook::find('auditory_memo_1')->getList();

$noteModel = NoticeConsultDisplay::getData($dataProvider->models, $model->plan_year);

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
        'group' => true,
    ],
    [
        'attribute' => 'sect_name',
        'width' => '320px',
        'value' => function ($model) {
            return $model->sect_name != 'Индивидуально' ? $model->sect_name . $model->getSectNotice() : $model->sect_name;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'format' => 'raw',
        'footer' => 'ИТОГО: ак.час',
    ],
    [
        'attribute' => 'year_time_consult',
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'group' => true,
        'subGroupOf' => 1,
        'footer' => $noteModel->getTotal('year_time_consult'),
    ],
    [
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) use($teachers_list) {
            $teachers_fio = $teachers_list[$model->teachers_id] ?? '';
            return \artsoft\Art::isBackend() ?  Html::a($teachers_fio,
                ['/teachers/default/consult-items', 'id' => $model->teachers_id],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
//                    'class' => 'btn btn-info',
                ]) : $teachers_fio;
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'load_time_consult',
        'value' => function ($model) use ($noteModel) {
            return $model->load_time_consult . ' ' . $noteModel->getTeachersConsultOverLoadNotice($model);
        },
        'format' => 'raw',
        'footer' => $noteModel->getTotal('load_time_consult'),
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'datetime_in',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_in;
        },
        'footer' => $noteModel->getTotal('datetime_in'),
        'format' => 'raw',
    ],
    [
        'attribute' => 'datetime_out',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_out;
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'auditory_id',
        'width' => '300px',
        'value' => function ($model) use ($auditory_list, $noteModel) {
            $auditory = $auditory_list[$model->auditory_id] ?? '';
            return $auditory != '' ? $auditory . $noteModel->getConsultOverLoopingNotice($model) : '';
        },
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'visible' => \artsoft\Art::isBackend(),
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/consult-items', 'id' => $model->studyplan_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/consult-items', 'id' => $model->studyplan_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/consult-items', 'id' => $model->studyplan_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'delete']), [
                        'title' => Yii::t('art', 'Delete'),
                        'aria-label' => Yii::t('art', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
        'visibleButtons' => [
            'create' => function ($model) use($noteModel) {
                return $noteModel->getTeachersConsultScheduleNeed($model);
            },
            'delete' => function ($model) {
                return $model->consult_schedule_id !== null;
            },
            'update' => function ($model) {
                return $model->consult_schedule_id !== null;
            }
        ],
    ],
];
?>
<div class="consult-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Расписание консультаций: <?= RefBook::find('students_fullname')->getValue($model->student_id);?>
            <?= $model->getProgrammName() . ' - ' . $model->course . ' класс.';?>
        </div>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'consult-schedule-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'consult-schedule-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'consult-schedule-grid',
                'pjax' => false,
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'showPageSummary' => false,
                'showFooter' => true,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 4, 'class' => 'text-center info']],
                            ['content' => 'Расписание консультаций', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
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

