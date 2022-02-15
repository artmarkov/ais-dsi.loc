<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\LessonItems;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\LessonItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Lesson Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-items-index">
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
                                'model' => LessonItems::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?=  GridPageSize::widget(['pjaxId' => 'lesson-items-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'lesson-items-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'lesson-items-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'lesson-items-grid',
                            'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/lesson-items/default',
                                'title' => function(LessonItems $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

            'id',
            'subject_sect_studyplan_id',
            'studyplan_subject_id',
            'lesson_test_id',
            'lesson_date',
            // 'lesson_topic',
            // 'lesson_rem',
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


