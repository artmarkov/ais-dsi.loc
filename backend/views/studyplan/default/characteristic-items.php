<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\SubjectCharacteristicViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Characteristic');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'width' => '320px',
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
        'group' => true,
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
    ],
    'description:text',
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/characteristic-items', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/characteristic-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_characteristic_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/characteristic-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_characteristic_id, 'mode' => 'delete']), [
                        'title' => Yii::t('art', 'Delete'),
                        'aria-label' => Yii::t('art', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
        'visibleButtons' => [
            'create' => function ($model) {
                return true;
            },
            'delete' => function ($model) {
                return $model->subject_characteristic_id;
            },
            'update' => function ($model) {
                return $model->subject_characteristic_id;
            }
        ],
    ],
];
?>
<div class="subject-characteristic-index">
    <div class="panel">
        <div class="panel-heading">
            Характерисики по предметам: <?= RefBook::find('students_fio')->getValue($model->student_id);?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => SubjectSect::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-characteristic-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'subject-characteristic-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'subject-characteristic-grid',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table-condensed'],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Дисциплина', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                            ['content' => 'Характеристика', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

