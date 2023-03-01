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
                        'bulkActionOptions' => [
                            'gridId' => 'document-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'rowOptions' => function(Document $model) {
                            if($model->getFilesCount() == 0) {
                                return ['class' => 'danger'];
                            }
                            return [];
                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'options' => ['style' => 'width:10px'],
                                'value' => function (Document $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'title',
                                'value' => function (Document $model) {
                                    return $model->title;
                                },
                            ],
                            [
                                'attribute' => 'fullName',
                                'value' => function (Document $model) {
                                    return $model->userCommon->getFullName();
                                },
                            ],
                            'doc_date:date',
                            [
                                'attribute' => 'countFiles',
                                'value' => function (Document $model) {
                                    return $model->getFilesCount();
                                },
                            ],
//                            'description',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/info/document',
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

