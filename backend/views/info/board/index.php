<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\info\Board;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\info\search\BoardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/info', 'Board');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="board-index">
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
                                'model' => Board::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'board-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'board-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'board-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'board-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Board $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'title',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/info/board',
                                'title' => function (Board $model) {
                                    return Html::a(sprintf($model->title), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            [
                                'attribute' => 'category_id',
                                'value' => function (Board $model) {
                                    return Board::getCategoryValue($model->category_id);
                                },

                            ],
                            'author_id',
                            [
                                'attribute' => 'recipients_list',
                                'filter' => Board::getRecipientsList(),
                                'value' => function (Board $model) {
                                    $v = [];
                                    foreach ($model->recipients_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = \common\models\user\UserCommon::findOne($id)->getFullName();
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'board_date',
                            'delete_date',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'importance_id',
                                'optionsArray' => [
                                    [Board::IMPORTANCE_HI, Yii::t('art/info', 'Hi'), 'success'],
                                    [Board::IMPORTANCE_NORM, Yii::t('art/info', 'Normal'), 'primary'],
                                    [Board::IMPORTANCE_LOW, Yii::t('art/info', 'Low'), 'default'],
                                ],
                                'options' => ['style' => 'width:120px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Board::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [Board::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                                ],
                                'options' => ['style' => 'width:120px']
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


