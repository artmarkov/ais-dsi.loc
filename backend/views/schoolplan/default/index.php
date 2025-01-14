<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\own\Department;
use common\models\user\UserCommon;
use yii\db\Query;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\Schoolplan;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'School Plans');
$this->params['breadcrumbs'][] = $this->title;
$executorsBonus = Schoolplan::getEfficiencyForExecutors($dataProvider->models);
$teachers_list = RefBook::find('teachers_fio',1)->getList();
$department_list =  \yii\helpers\ArrayHelper::map(Department::find()->select('id, name')->asArray()->all(), 'id', 'name');
$signer_list = \yii\helpers\ArrayHelper::map((new Query())->from('users_view')
    ->select('id , user_name as name')
    ->where(['=', 'status', User::STATUS_ACTIVE])
    ->all(), 'id', 'name');
$author_id = Schoolplan::getAuthorId();
?>
<div class="schoolplan-plan-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <?= $this->render('_search', compact('model_date')) ?>
        <div class="panel-body">
            <?php if (\artsoft\Art::isFrontend()): ?>
                <?php echo \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> Этим цветом помечены авторские записи',
                    'options' => ['class' => 'alert-warning'],
                ]);
                ?>
            <?php endif; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => ActivitiesPlan::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'schoolplan-plan-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'schoolplan-plan-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'schoolplan-plan-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
//                        'bulkActionOptions' => [
//                            'gridId' => 'schoolplan-plan-grid',
//                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
//                        ],
                        'rowOptions' => function (Schoolplan $model) use ($author_id) {
                            if ( $author_id == $model->author_id) {
                                return ['class' => 'warning'];
                            }
                            return [];
                        },
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
//                            [
//                                'attribute' => 'id',
//                                'value' => function (Schoolplan $model) {
//                                    return sprintf('#%06d', $model->id);
//                                },
//                                'contentOptions' => function (Schoolplan $model) {
//                                    switch ($model->doc_status) {
//                                        case Schoolplan::DOC_STATUS_DRAFT:
//                                            return ['class' => 'default'];
//                                        case Schoolplan::DOC_STATUS_AGREED:
//                                            return ['class' => 'success'];
//                                        case Schoolplan::DOC_STATUS_WAIT:
//                                            return ['class' => 'warning'];
//                                    }
//                                },
//                            ],
                        // столбцы только на экпорт
                            [
                                'value' => function (Schoolplan $model) {
                                    return Yii::$app->formatter->asDate($model->datetime_in);
                                },
                                'format' => 'raw',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'label' => 'Дата начала мероприятия'
                            ],
                            [
                                'value' => function (Schoolplan $model) {
                                    return Yii::$app->formatter->asDatetime($model->datetime_in,'php:H:i');
                                },
                                'format' => 'raw',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'label' => 'Время начала мероприятия'
                            ],
                            [
                                'value' => function (Schoolplan $model) {
                                    return Yii::$app->formatter->asDate($model->datetime_out);
                                },
                                'format' => 'raw',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'label' => 'Дата окончания мероприятия'
                            ],
                            [
                                'value' => function (Schoolplan $model) {
                                    return Yii::$app->formatter->asDatetime($model->datetime_out,'php:H:i');
                                },
                                'format' => 'raw',
                                'hidden' => true,
                                'hiddenFromExport'=> false,
                                'label' => 'Время окончания мероприятия'
                            ],
                            // end столбцы только на экпорт
                            [
                                'attribute' => 'datetime_in',
                                'value' => function (Schoolplan $model) {
                                    return $model->datetime_in . ' - </br>' . $model->datetime_out;
                                },
                                'options' => ['style' => 'width:150px'],
                                'format' => 'raw',
                                'hiddenFromExport'=> true,
                                'label' => 'Дата мероприятия'
                            ],
                            [
                                'attribute' => 'title',
                                'value' => function (Schoolplan $model) {
                                    return $model->title;
                                },
                                'options' => ['style' => 'width:450px'],
                            ],
                            [
                                'attribute' => 'category_id',
                                'value' => function ($model) {
                                    return $model->categoryName;
                                },
                                'options' => ['style' => 'width:350px', 'class' => 'danger'],
                                'filter' => \common\models\guidesys\GuidePlanTree::getPlanList(),
                            ],
                            'auditory_places',
                            [
                                'attribute' => 'department_list',
                                'filter' => Department::getDepartmentList(),
                                'value' => function (Schoolplan $model) use ($department_list) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = $department_list[$id] ?? '';
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'executors_list',
                                'filter' => $teachers_list,
                                'filterType' => GridView::FILTER_SELECT2,
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'value' => function (Schoolplan $model) use ($executorsBonus, $teachers_list) {
                                    $v = [];
                                    foreach ($model->executors_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                    $teachers_fio = $teachers_list[$id] ?? $id;
                                        $v[] = $teachers_fio . (\artsoft\Art::isBackend() ? (isset($executorsBonus[$model->id][$id]) ? $executorsBonus[$model->id][$id] : null) : null);
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'result',
                                'value' => function ($model) {
                                    return mb_strlen($model->result, 'UTF-8') > 200 ? mb_substr($model->result, 0, 200, 'UTF-8') . '...' : $model->result;
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'num_users',
                                'label' => 'Участ.',
                                'value' => function ($model) {
                                    return $model->num_users;
                                },
                            ],
                            [
                                'attribute' => 'num_winners',
                                'label' => 'Поб.',
                                'value' => function ($model) {
                                    return $model->num_winners;
                                },
                            ],
                            [
                                'attribute' => 'num_visitors',
                                'label' => 'Зрит.',
                                'value' => function ($model) {
                                    return $model->num_visitors;
                                },
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/schoolplan/default',
                                'template' => '{view} {update} {clone} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'buttons' => [
                                    'clone' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span>',
                                            ['/schoolplan/default/create', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Clone'),
                                                'data-method' => 'post',
                                                'data-confirm' => Yii::t('art', 'Are you sure you want to clone this item?'),
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                ],
                                'visibleButtons' => [
                                    'update' => function ($model) use ($author_id) {
                                        return $author_id == $model->author_id || \artsoft\Art::isBackend();
                                    },
                                    'clone' => function ($model) use ($author_id) {
                                        return $author_id == $model->author_id || \artsoft\Art::isBackend();
                                    },
                                    'delete' => function ($model) use ($author_id) {
                                        return $author_id == $model->author_id || \artsoft\Art::isBackend();
                                    },
                                    'view' => function ($model) {
                                        return true;
                                    }
                                ],
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'doc_status',
                                'optionsArray' => [
                                    [Schoolplan::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
                                    [Schoolplan::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
                                    [Schoolplan::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
                                    [Schoolplan::DOC_STATUS_MODIF, Yii::t('art', 'Modif'), 'warning'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'attribute' => 'signer_id',
                                'value' => function (Schoolplan $model) use($signer_list) {
                                    return $signer_list[$model->signer_id] ?? '';
                                },
                                'options' => ['style' => 'width:150px'],
                                'contentOptions' => ['style' => "text-align:center; vertical-align: middle;"],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'bars_flag',
                                'label' => 'БАРС',
                                'visible' => \artsoft\Art::isBackend(),
                                'value' => function (Schoolplan $model) {
                                    return $model->bars_flag == 1 ? '<i class="fa fa-thumbs-up text-success" style="font-size: 1.5em;"></i> Да'
                                        : '<i class="fa fa-thumbs-down text-danger" style="font-size: 1.5em;"></i> Нет';
                                },
                                'contentOptions' => ['style' => 'text-align:center; vertical-align: middle;'],
                                'format' => 'raw',
                            ],
                            [
                                'label' => 'Вып.плана',
                                'visible' => \artsoft\Art::isBackend(),
                                'value' => function (Schoolplan $model) {
                                    return $model->schoolplanPerform ? Html::a('<i class="fa fa-thumbs-up text-success" style="font-size: 1.5em;"></i> ' . count($model->schoolplanPerform),
                                        ['/schoolplan/default/perform', 'id' => $model->id],
                                        [
                                            'data-pjax' => '0',
                                        ]) : '<i class="fa fa-thumbs-down text-danger" style="font-size: 1.5em;"></i> Нет';
                                },
                                'contentOptions' => ['style' => 'text-align:center; vertical-align: middle;'],
                                'format' => 'raw',
                            ],
                            [
                                'label' => 'Файл',
                                'visible' => \artsoft\Art::isBackend(),
                                'value' => function (Schoolplan $model) {
                                    return $model->getFilesCount() ? Html::a('<i class="fa fa-thumbs-up text-success" style="font-size: 1.5em;"></i> ' . $model->getFilesCount(),
                                        ['/schoolplan/default/view', 'id' => $model->id, '#' => 'file'],
                                        [
                                            'data-pjax' => '0',
                                        ]) :
                                        '<i class="fa fa-thumbs-down text-danger" style="font-size: 1.5em;"></i> Нет';
                                },
                                'contentOptions' => ['style' => 'text-align:center; vertical-align: middle;'],
                                'format' => 'raw',
                            ],

                        ],
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Мероприятие', 'options' => ['colspan' => 6, 'class' => 'text-center warning']],
                                    ['content' => 'Итоги/Количество', 'options' => ['colspan' => 4, 'class' => 'text-center success']],
                                    ['content' => 'Статус', 'options' => ['colspan' => 6, 'class' => 'text-center danger']],
                                ],
//                                'options' => ['class' => 'skip-export'] // remove this row from export
                            ]
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


