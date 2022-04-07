<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\info\Document;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\info\search\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $employees_id */

$this->title = Yii::t('art/guide', 'Documents');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">
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
                                'model' => Document::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'document-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'document-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'document-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'rowOptions' => function(Document $model) {
                            if($model->getFilesCount() == 0) {
                                return ['class' => 'danger'];
                            }
                            return [];
                        },
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'options' => ['style' => 'width:10px'],
                                'value' => function (Document $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'title',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/employees/default',
                                'title' => function ($model) use ($employees_id) {
                                    return Html::a($model->title, ['/employees/default/document', 'id' => $employees_id, 'objectId' => $model->id, 'mode' => 'view'], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) use ($employees_id) {
                                        return  Html::a(Yii::t('art', 'Edit'),
                                            Url::to(['/employees/default/document', 'id' => $employees_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'view' => function ($url, $model, $key) use ($employees_id) {
                                        return  Html::a(Yii::t('art', 'View'),
                                            Url::to(['/employees/default/document', 'id' => $employees_id, 'objectId' => $model->id, 'mode' => 'view']), [
                                                'title' => Yii::t('art', 'View'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model, $key) use ($employees_id) {
                                        return Html::a(Yii::t('art', 'Delete'),
                                            Url::to(['/employees/default/document', 'id' => $employees_id, 'objectId' => $model->id, 'mode' => 'delete']), [
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

//                            'user_common_id',
//                            'description',
                            'doc_date:date',
                            [
                                'attribute' => 'countFiles',
                                'value' => function (Document $model) {
                                    return $model->getFilesCount();
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
<?php
\artsoft\widgets\DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'doc_date',
    'format' => 'DD.MM.YYYY',
    'opens' => 'left',
])
?>

