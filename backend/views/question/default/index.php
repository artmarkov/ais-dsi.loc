<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\question\Question;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\question\search\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/question', 'Questions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                     echo GridQuickLinks::widget([
                        'model' => Question::className(),
                        'searchModel' => $searchModel,
                    ])
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'question-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'question-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'question-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'question-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (Question $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function (Question $model) {
                            return $model->name;
                        },
                    ],
                    [
                        'attribute' => 'category_id',
                        'filter' => Question::getCategoryList(),
                        'value' => function (Question $model) {
                            return Question::getCategoryValue($model->category_id);
                        },

                    ],
                    [
                        'attribute' => 'users_cat',
                        'filter' => Question::getGroupList(),
                        'value' => function (Question $model) {
                            return Question::getGroupValue($model->users_cat);
                        },

                    ],
                    [
                        'attribute' => 'vid_id',
                        'filter' => Question::getVidList(),
                        'value' => function (Question $model) {
                            return Question::getVidValue($model->vid_id);
                        },

                    ],
                    [
                        'attribute' => 'division_list',
                        'filter' => RefBook::find('division_name')->getList(),
                        'value' => function (Question $model) {
                            $v = [];
                            foreach ($model->division_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = RefBook::find('division_name')->getValue($id);
                            }
                            return implode(';<br/> ', $v);
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'timestamp_in:datetime',
                    'timestamp_out:datetime',
                    [
                        'attribute' => 'author_id',
                        'filter' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'value' => function (Question $model) {
                            return $model->author->userCommon ? $model->author->userCommon->fullName : $model->author_id;
                        },
                        'format' => 'raw',
                        'visible' => User::hasPermission('editBoardAuthor'),
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Question::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [Question::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/question/default',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],

                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


