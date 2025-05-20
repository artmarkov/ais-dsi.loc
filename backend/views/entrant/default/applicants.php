<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\education\EntrantProgramm;
use common\models\subject\Subject;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="entrant-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
            <?= Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Добавить из файла',
                ['/entrant/default/applicants', 'id' => $id, 'mode' => 'import'], [
                    'class' => 'btn btn-sm btn-warning',
                    'title' => 'Добавить из файла',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]
            );
            ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Entrant::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' =>  \artsoft\models\User::hasRole('entrantAdmin') ? [
                    'gridId' => 'entrant-grid',
                    'actions' => \artsoft\Art::isBackend() ? [
                        Url::to(['applicants-bulk-waiting']) => 'Перевести в статус "В ожидании испытаний"',
                        Url::to(['applicants-bulk-open']) => 'Перевести в статус "Испытания открыты"',
                        Url::to(['applicants-bulk-close']) => 'Перевести в статус "Испытания завершены"',
                        Url::to(['applicants-bulk-make']) => 'Сформировать учебный план',
                        Url::to(['applicants-bulk-delete']) => Yii::t('art', 'Delete')
                    ] : [
                        Url::to(['applicants-bulk-waiting']) => 'Перевести в статус "В ожидании испытаний"',
                        Url::to(['applicants-bulk-open']) => 'Перевести в статус "Испытания открыты"',
                        Url::to(['applicants-bulk-close']) => 'Перевести в статус "Испытания завершены"',
                    ]
                    //Configure here you bulk actions
                ] : false,
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['value' => $model->id];
                    },
                        'visible' => \artsoft\models\User::hasRole('entrantAdmin'),
                    ],
                    [
                        'attribute' => 'group_id',
                        'filter' => \common\models\entrant\Entrant::getCommGroupList($id),
                        'value' => function (\common\models\entrant\Entrant $model) use ($id) {
                            return \common\models\entrant\Entrant::getCommGroupValue($id, $model->group_id);
                        },
                        'format' => 'raw',
                        'group' => true
                    ],
//                    [
//                        'attribute' => 'id',
//                        'value' => function (Entrant $model) {
//                            return sprintf('#%06d', $model->id);
//                        },
//                        'options' => ['style' => 'width:100px'],
//                        'visible' => Yii::$app->user->isSuperadmin,
//                    ],
                    [
                        'attribute' => 'fullname',
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return \artsoft\Art::isBackend() ? Html::a($model->fullname,
                                ['/students/default/view', 'id' => $model->student_id], ['title' => 'Перейти в реестр', 'target' => '_blank', 'data-pjax' => 0])
                                : Html::a($model->fullname, ['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'update']);
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'birth_date',
                        'filter' => false,
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            $age = \artsoft\helpers\ArtHelper::age($model->birth_date);
                            return $model->birth_date ? Yii::$app->formatter->asDate($model->birth_date) . ' (' . $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.)' : '';
                        },
                        'headerOptions' => ['style' =>'white-space:pre-line;'],
                        'format' => 'raw'
                    ],
//                    [
//                        'attribute' => 'student_id',
//                        'filter' => RefBook::find('students_fullname')->getList(),
//                        'value' => function (Entrant $model) {
//                            return RefBook::find('students_fullname')->getValue($model->student_id);
//                        },
//                        'format' => 'raw'
//                    ],
//            'comm_id',
                    [
                        'attribute' => 'subject_list',
                        'filter' => \common\models\subject\Subject::getSubjectByCategory(1000),
                        'value' => function (Entrant $model) {
                            $v = [];
                            foreach ($model->subject_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = RefBook::find('subject_name')->getValue($id) ?? '';
                            }
                            return implode('<br/> ', $v);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['style' =>'white-space:pre-line;'],
                        'label' => 'Выбранные дисциплины'
                    ],
                     [
                         'attribute' => 'last_experience',
                         'value' => function (Entrant $model) {
                             return $model->last_experience;
                         },
                         'format' => 'raw',
                         'label' => 'Прим.'
                     ],
                    [
                        'attribute' => 'mid_mark',
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return str_replace('.', ',', round($model->mid_mark, 2)); // str_replace для импорта в xlsx
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'Ср.оц.'
                    ],
                    [
                        'attribute' => 'programm_id',
                        'contentOptions' => function (Entrant $model) {
                            return ['class' => $model->decision_id == 1 ? 'success' : 'default'];
                        },
                        'filter' => RefBook::find('education_programm_short_name')->getList(),
//                        'filterType' => GridView::FILTER_SELECT2,
//                        'filterWidgetOptions' => [
//                            'pluginOptions' => ['allowClear' => true],
//                        ],
//                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return RefBook::find('education_programm_short_name')->getValue($model->programm_id) ?? '';
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'План'
                    ],
                    [
                        'attribute' => 'subject_id',
                        'contentOptions' => function (Entrant $model) {
                            return ['class' => $model->decision_id == 1 ? 'success' : 'default'];
                        },
                        'filter' => \common\models\subject\Subject::getSubjectByCategory(1000),
//                        'filterType' => GridView::FILTER_SELECT2,
//                        'filterWidgetOptions' => [
//                            'pluginOptions' => ['allowClear' => true],
//                        ],
//                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return RefBook::find('subject_name')->getValue($model->subject_id) ?? '';
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'Спец-ть'
                    ],
                    [
                        'attribute' => 'course',
                        'contentOptions' => function (Entrant $model) {
                            return ['class' => $model->decision_id == 1 ? 'success' : 'default'];
                        },
                        'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
//                        'filterType' => GridView::FILTER_SELECT2,
//                        'filterWidgetOptions' => [
//                            'pluginOptions' => ['allowClear' => true],
//                        ],
//                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course] ?? '';
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'Курс'
                    ],
                    [
                        'attribute' => 'subject_form_id',
                        'contentOptions' => function (Entrant $model) {
                            return ['class' => $model->decision_id == 1 ? 'success' : null];
                        },
                        'filter' => RefBook::find('subject_form_name')->getList(),
//                        'filterType' => GridView::FILTER_SELECT2,
//                        'filterWidgetOptions' => [
//                            'pluginOptions' => ['allowClear' => true],
//                        ],
//                        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                        'value' => function (\common\models\entrant\EntrantView $model) {
                            return \common\models\subject\SubjectForm::getFormValue($model->subject_form_id) ?? '';
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'Форма'
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'decision_id',
                        'optionsArray' => [
                            [0, 'Не обработано', 'default'],
                            [1, 'Рекомендован', 'success'],
                            [2, 'Не рекомендован', 'danger'],
                        ],
                        'options' => ['style' => 'width:120px'],
                        'visible' => User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend(),
                        'label' => 'Решение'
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [0, 'В ожидании', 'default'],
                            [1, 'Открыты', 'success'],
                            [2, 'Завершены', 'warning'],
                        ],
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'controller' => '/entrant/default/applicants',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/entrant/default/applicants', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


