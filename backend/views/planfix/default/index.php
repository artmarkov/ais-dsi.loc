<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\planfix\Planfix;
use common\models\planfix\PlanfixCategory;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\planfix\search\PlanfixSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art', 'Planfix');
$this->params['breadcrumbs'][] = $this->title;

$users_list = artsoft\models\User::getUsersListByCategory(['teachers', 'employees'], false);
?>
<div class="planfix-index">
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
                        'model' => Planfix::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'planfix-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'planfix-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'planfix-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'planfix-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'options' => ['style' => 'width:30px'],
                        'value' => function (Planfix $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'filter' => PlanfixCategory::getPlanfixCategoryList(),
                        'attribute' => 'category_id',
                        'value' => function (Planfix $model) {
                            return PlanfixCategory::getPlanfixCategoryValue($model->category_id);
                        },
                    ],
                    'name',
//                    'description:ntext',
                    [
                        'attribute' => 'planfix_author',
                        'filter' => $users_list,
                        'value' => function (Planfix $model) use ($users_list) {
                            return $users_list[$model->planfix_author] ?? $model->planfix_author;

                        },
                        'options' => ['style' => 'width:150px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'executors_list',
                        'filter' => $users_list,
                        'value' => function (Planfix $model) use ($users_list) {
                            $v = [];
                            foreach ($model->executors_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = $users_list[$id] ?? $id;
                            }
                            return implode(', ', $v);

                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'created_at:date',
                    'planfix_date:date',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'importance',
                        'optionsArray' => Planfix::getImportanceOptionsList(),
                        'options' => ['style' => 'width:120px']
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => Planfix::getStatusOptionsList(),
                        'options' => ['style' => 'width:120px']
                    ],
//                    'status_reason',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'visible' => \artsoft\Art::isBackend(),
                        'controller' => '/planfix/default',
                        'template' => '{view} {update} {delete}',/* */
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


