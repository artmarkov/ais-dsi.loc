<?php

use artsoft\grid\SortableGridView;
use artsoft\helpers\RefBook;
use yii\widgets\Pjax;
use artsoft\grid\GridQuickLinks;
use common\models\education\LessonTest;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\LessonTestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Lesson Tests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-test-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            echo GridQuickLinks::widget([
                                'model' => LessonTest::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'lesson-test-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'lesson-test-grid-pjax',
                    ])
                    ?>

                    <?=
                    SortableGridView::widget([
                        'id' => 'piece-category-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'lesson-test-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/guidestudy/lesson-test',
                                'title' => function (LessonTest $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            [
                                'attribute' => 'division_list',
                                'filter' => RefBook::find('division_name')->getList(),
                                'value' => function (LessonTest $model) {
                                    $v = [];
                                    foreach ($model->division_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('division_name')->getValue($id);
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'test_category',
                                'filter' => LessonTest::getTestCatogoryList(),
                                'value' => function (LessonTest $model) {
                                    return LessonTest::getTestCatogoryValue($model->test_category);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'test_name',
                            'test_name_short',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'plan_flag',
                                'optionsArray' => [
                                    [1, Yii::t('art', 'Yes'), 'success'],
                                    [0, Yii::t('art', 'No'), 'danger'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [LessonTest::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [LessonTest::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:60px']
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


