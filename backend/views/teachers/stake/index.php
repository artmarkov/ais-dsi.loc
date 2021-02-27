<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Stake;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Stake');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stake-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/teachers/stake/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                      'model' => Stake::className(),
                      'searchModel' => $searchModel,
                      ]) */
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?//= GridPageSize::widget(['pjaxId' => 'stake-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'stake-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'stake-grid',
                'dataProvider' => $dataProvider,
                'bulkActionOptions' => [
                    'gridId' => 'stake-grid',
                    //'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => '/teachers/stake',
                        'title' => function(Stake $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                },
                        'buttonsTemplate' => '{update} {delete}',
                    ],
//            'id',
//            'name',
                    'slug',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Stake::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [Stake::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:60px']
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


