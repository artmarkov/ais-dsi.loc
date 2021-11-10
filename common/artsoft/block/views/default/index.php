<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridQuickLinks;
use artsoft\grid\SortableGridView;
use artsoft\helpers\Html;
use artsoft\models\User;
use artsoft\block\models\Block;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel artsoft\block\models\search\BlockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/block', 'HTML Blocks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton('block/create'); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'page-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php Pjax::begin(['id' => 'block-grid-pjax']) ?>

                    <?= SortableGridView::widget([
                        'id' => 'block-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'block-grid',
                            'actions' => [
                                Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),
                            ]
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'title',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/block/default',
                                'title' => function (Block $model) {
                                    return Html::encode($model->title);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            'slug',
                            'title',
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


