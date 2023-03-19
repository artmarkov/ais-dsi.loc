<?php

use artsoft\helpers\RefBook;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($model) {
            return $model->studyplan_subject_id != 0 ? RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id) : RefBook::find('sect_memo_2')->getValue($model->subject_sect_studyplan_id);
        },
        'group' => true,
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'width' => '310px',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => RefBook::find('sect_name_3')->getList(),
        'value' => function ($model, $key, $index, $widget) {
            return RefBook::find('sect_name_3')->getValue($model->subject_sect_studyplan_id) ?? 'Индивидуально';
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'week_time',
        'value' => function ($model) {
            return $model->week_time;
        },
        'group' => true,
        'subGroupOf' => 2,
    ],
    [
        'attribute' => 'studyplan_subject_list',
        'width' => '310px',
        'filter' => RefBook::find('students_fio')->getList(),
        'filterType' => GridView::FILTER_SELECT2,
        'value' => function ($model, $key, $index, $widget) {
            $data = [];
            if (!empty($model->studyplan_subject_list)) {
                foreach (explode(',', $model->studyplan_subject_list) as $item => $studyplan_subject_id) {
                    $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
                    $data[] = RefBook::find('students_fio')->getValue($student_id);
                }
            }
            return implode(',', $data);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'direction_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \common\models\guidejob\Direction::getDirectionList(),
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'teachers_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => false /*RefBook::find('teachers_fio')->getList()*/,
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time . ' ' . $model->getItemLoadNotice();
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'scheduleDisplay',
        'value' => function ($model) {
            return $model->getScheduleDisplay();
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'auditory_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => RefBook::find('auditory_memo_1')->getList(),
        'value' => function ($model) {
            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
    ],
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-body">
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                'tableOptions' => ['class' => 'table-condensed'],
//                        'showPageSummary' => true,
//                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => 'Элементы расписания',
                    'type' => 'default',
                    'after' => '',
                ],
                'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Расписание занятий', 'options' => ['colspan' => 2, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
                'toolbar' => [],
            ]);
            ?>
        </div>
    </div>
</div>

