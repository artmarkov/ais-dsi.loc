<?php

use artsoft\helpers\RefBook;
use common\models\own\Department;
use common\models\user\UserCommon;
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
                        'rowOptions' => function(Schoolplan $model) {
                            if($model->isAuthor()) {
                                return ['class' => 'warning'];
                            }
                            return [];
                        },
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Schoolplan $model) {
                                    return sprintf('#%06d', $model->id);
                                },
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
                            ],
                            [
                                'attribute' => 'datetime_in',
                                'value' => function (Schoolplan $model) {
                                    return $model->datetime_in . ' - </br>' . $model->datetime_out;
                                },
                                'options' => ['style' => 'width:150px'],
                                'format' => 'raw',
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
                                'value' => function (Schoolplan $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Department::findOne($id)->name;
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'executors_list',
                                'filter' => RefBook::find('teachers_fio', UserCommon::STATUS_ACTIVE)->getList(),
                                'value' => function (Schoolplan $model) use ($executorsBonus){
                                    $v = [];
                                    foreach ($model->executors_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('teachers_fio')->getValue($id) . (\artsoft\Art::isBackend() ? (isset($executorsBonus[$model->id][$id]) ? $executorsBonus[$model->id][$id] : null) : null);
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'result',
                                'value' => function ($model) {
                                    return  mb_strlen($model->result, 'UTF-8') > 200 ? mb_substr($model->result, 0, 200, 'UTF-8') . '...' : $model->result;
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'num_users',
                                'label' => 'Число уч.',
                                'value' => function ($model) {
                                    return $model->num_users ;
                                },
                            ],
                            [
                                'attribute' => 'num_winners',
                                'label' => 'Число поб.',
                                'value' => function ($model) {
                                    return $model->num_winners;
                                },
                            ],
                            [
                                'attribute' => 'num_visitors',
                                'label' => 'Число зрит.',
                                'value' => function ($model) {
                                    return $model->num_visitors;
                                },
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'doc_status',
                                'optionsArray' => [
                                    [Schoolplan::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
                                    [Schoolplan::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
                                    [Schoolplan::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'attribute' => 'bars_flag',
                                'label' => 'БАРС',
                                'value' => function (Schoolplan $model) {
                                    return $model->bars_flag == 1 ? '<i class="fa fa-thumbs-up text-success" style="font-size: 1.5em;"></i>Да' : '<i class="fa fa-thumbs-down text-danger" style="font-size: 1.5em;"></i>Нет';
                                },
                                'contentOptions' => ['style' => 'text-align:center; vertical-align: middle;'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/schoolplan/default',
                                'template' => '{view} {update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'visibleButtons' => [
                                    'update' => function ($model) {
                                        return ($model->isAuthor() && $model->doc_status == Schoolplan::DOC_STATUS_DRAFT) || \artsoft\Art::isBackend();
                                    },
                                    'delete' => function ($model) {
                                        return ($model->isAuthor() && $model->doc_status == Schoolplan::DOC_STATUS_DRAFT) || \artsoft\Art::isBackend();
                                    },
                                    'view' => function ($model) {
                                        return true;
                                    }
                                ],
                            ],
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


