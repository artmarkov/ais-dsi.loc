<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\LessonProgress;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\LessonProgressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Lesson Progresses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-progress-index">
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
                            /* echo GridQuickLinks::widget([
                                'model' => LessonProgress::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?=  GridPageSize::widget(['pjaxId' => 'lesson-progress-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'lesson-progress-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'lesson-progress-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'lesson-progress-grid',
                            'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/lesson-progress/default',
                                'title' => function(LessonProgress $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

            'id',
            'lesson_items_id',
            'studyplan_subject_id',
            'lesson_test_id',
            'mark',
            // 'mark_rem',
            // 'created_at',
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'version',

                ],
            ]);
            ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


