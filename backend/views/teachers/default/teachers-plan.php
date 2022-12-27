<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Teachers Plan');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-plan-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Планирование инд.занятий: <?php echo RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
                </div>
                <div class="panel-body">
                    <?= $this->render('_search', compact('model_date')) ?>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>

                            <?php

                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => TeachersPlan::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'teachers-plan-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'teachers-plan-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'teachers-plan-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'teachers-plan-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/teachers/teachers-plan',
                                'title' => function (TeachersPlan $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update'], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($key, $model) {
                                        return Html::a(Yii::t('art', 'Update'),
                                            Url::to(['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a(Yii::t('art', 'Delete'),
                                            Url::to(['/teachers/default/teachers-plan', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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

                            [
                                'attribute' => 'direction_id',
                                'filter' => \common\models\guidejob\Direction::getDirectionList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->direction ? $model->direction->name : null;
                                },

                            ],
                            //  'teachers_id',
                            [
                                'attribute' => 'planDisplay',
                                'value' => function ($model) {
                                    return $model->getPlanDisplay();
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'auditory_id',
                                'filter' => RefBook::find('auditory_memo_1')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                },
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


