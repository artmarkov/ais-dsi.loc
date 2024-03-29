<?php

use artsoft\helpers\RefBook;
use common\models\own\Department;
use common\models\user\UserCommon;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\activities\ActivitiesOver;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesOverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Activities Overs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-over-index">
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
                        'model' => ActivitiesOver::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'activities-over-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'activities-over-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'activities-over-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'activities-over-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'rowOptions' => function (ActivitiesOver $model) {
                    if ($model->executor_name != null) {
                        return ['class' => 'warning'];
                    }
                    return [];
                },
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (ActivitiesOver $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'title',
                        'options' => ['style' => 'width:300px'],
                        'value' => function (ActivitiesOver $model) {
                            return $model->title;
                        },
                    ],
                    [
                        'attribute' => 'over_category',
                        'options' => ['style' => 'width:200px'],
                        'filter' => ActivitiesOver::getOverCategoryList(),
                        'value' => function ($model) {
                            return ActivitiesOver::getOverCategoryValue($model->over_category);
                        },
                    ],
                    [
                        'attribute' => 'auditory_id',
                        'options' => ['style' => 'width:200px'],
                        'filter' => RefBook::find('auditory_memo_1', 1, true)->getList(),
                        'value' => function ($model) {
                            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                        },
                    ],
                    [
                        'attribute' => 'department_list',
                        'filter' => Department::getDepartmentList(),
                        'value' => function (ActivitiesOver $model) {
                            $v = [];
                            foreach ($model->department_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = Department::findOne($id)->name;
                            }
                            return implode('<br/> ', $v);
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'executors_list',
                        'filter' => RefBook::find('teachers_fio', UserCommon::STATUS_ACTIVE)->getList(),
                        'value' => function (ActivitiesOver $model) {
                            if ($model->executor_name != null) {
                                return $model->executor_name;
                             } else {
                                $v = [];
                                foreach ($model->executors_list as $id) {
                                    if (!$id) {
                                        continue;
                                    }
                                    $v[] = RefBook::find('teachers_fio')->getValue($id);
                                }
                                return implode(',<br/> ', $v);
                            }
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
//                    'description:ntext',
                    'datetime_in:datetime',
                    'datetime_out:datetime',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/activities/activities-over',
                        'template' => \artsoft\Art::isFrontend() ? '{view}' : '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],

                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>

<?php
\artsoft\widgets\DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'datetime_in',
    'format' => 'DD.MM.YYYY H:mm',
    'opens' => 'left',
])
?>
