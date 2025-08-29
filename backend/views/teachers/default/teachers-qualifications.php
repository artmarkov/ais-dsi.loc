<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\TeachersQualifications;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersQualificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelTeachers */

?>
<div class="teachers-qualifications-index">
    <div class="panel">
        <div class="panel-heading">
            Показатели ППК: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?= \artsoft\helpers\ButtonHelper::createButton(\artsoft\Art::isBackend() ? ['/teachers/default/qualifications', 'id' => $modelTeachers->id, 'mode' => 'create'] : ['/teachers/qualifications/create']); ?>
                    
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => TeachersQualifications::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'teachers-qualifications-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'teachers-qualifications-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
                'id' => 'teachers-qualifications-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
//                'bulkActionOptions' => \artsoft\Art::isBackend() ? [
//                    'gridId' => 'teachers-qualifications-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
//                ] : false,
                'columns' => [
//                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'visible' => \artsoft\Art::isBackend()],
                    [
                        'attribute' => 'id',
                        'options' => ['style' => 'width:10px'],
                        'value' => function (TeachersQualifications $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    
//                                [
//                                    'attribute' => 'teachers_id',
//                                    'value' => 'teachersName',
//                                    'label' => Yii::t('art/teachers', 'Teachers'),
//                                    'filter' => \artsoft\helpers\RefBook::find('teachers_fullname')->getList(),
//                                ],
                    [
                        'attribute' => 'name',
                        'value' => function (TeachersQualifications $model) {
                            return $model->name;
                        },
                    ],
                    [
                        'attribute' => 'place',
                        'value' => function (TeachersQualifications $model) {
                            return $model->place;
                        },
                    ],
                    [
                        'attribute' => 'date',
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                        'value' => function ($model) {
                            return $model->date;
                        },
                        'options' => ['style' => 'width:150px'],
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [TeachersQualifications::STATUS_ACTIVE, 'Пройдена', 'success'],
                            [TeachersQualifications::STATUS_INACTIVE, 'Планируется', 'info'],
                        ],
                        'options' => ['style' => 'width:100px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'visible' => \artsoft\Art::isBackend(),
                        'buttons' => [
                            'update' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/teachers/default/qualifications', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/teachers/default/qualifications', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/teachers/default/qualifications', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'delete'], [
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
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'visible' => \artsoft\Art::isFrontend(),
                        'buttons' => [
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/teachers/qualifications/view', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'update' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/teachers/qualifications/update', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/teachers/qualifications/delete', 'id' => $model->id], [
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
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>
<?php
if ($searchModel) {
    DateRangePicker::widget([
        'model' => $searchModel,
        'attribute' => 'date',
        'format' => 'DD.MM.YYYY',
        'opens' => 'left',
    ]);
}
?>