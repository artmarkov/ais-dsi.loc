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
$url = User::hasRole(['student']) ? '/question/student/new' : (User::hasRole(['teacher','department']) ? '/question/teachers/new' : (User::hasRole(['parents']) ? '/question/parent/new' : '/question/default/new'));

?>
<div class="question-index">
    <div class="panel">
        <div class="panel-heading">
            Формы и заявки
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Question::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'question-grid-pjax']) ?>
                </div>
            </div>

            <?php
            /* Pjax::begin([
                 'id' => 'question-grid-pjax',
             ])*/
            ?>

            <?=
            GridView::widget([
                // 'id' => 'question-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                /* 'bulkActionOptions' => [
                     'gridId' => 'question-grid',
                     'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                 ],*/
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (Question $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function ($model) use ($url){
                            return Html::a($model->name,
                                [$url, 'id' => $model->id],
                                [
                                    'title' => 'Создать заявку',
                                    'data-pjax' => '0',
                                    'visible' => true
                                ]);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'category_id',
                        'filter' => Question::getCategoryList(),
                        'value' => function (Question $model) {
                            return Question::getCategoryValue($model->category_id);
                        },

                    ],
                    'timestamp_in:date',
                    'timestamp_out:date',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{create}',
                        'buttons' => [
                            'create' => function ($key, $model) use ($url){
                                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                                    Url::to([$url, 'id' => $model->id]), [
                                        'title' => 'Создать заявку',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'visible' => true
                                    ]
                                );
                            },
                        ],
                        /* 'visibleButtons' => [
                             'create' => function ($model) {
                                 return Yii::$app->user->isGuest;
                             }
                         ],*/
                    ],
                ],
            ]);
            ?>

            <?php /* Pjax::end()*/ ?>
        </div>
    </div>
</div>


