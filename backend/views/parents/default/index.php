<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\parents\Parents;
use common\models\user\UserCommon;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\parents\search\ParentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/parents', 'Parents');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parents-index">
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
                                'model' => Parents::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?=  GridPageSize::widget(['pjaxId' => 'parents-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'parents-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'parents-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'parents-grid',
                            'actions' => [ Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (Parents $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'fullName',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/parents/default',
                                'title' => function (Parents $model) {
                                    return Html::a($model->fullName, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'userStatus',
                                'optionsArray' => [
                                    [UserCommon::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [UserCommon::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
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


