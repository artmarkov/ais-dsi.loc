<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\creative\CreativeCategory;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/creative','Creative Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['creative/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="creative-category-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/creative/category/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php 
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => CreativeCategory::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'creative-category-grid-pjax']) ?>
                </div>
            </div>

            <?php 
            Pjax::begin([
                'id' => 'creative-category-grid-pjax',
            ])
            ?>

            <?= 
            GridView::widget([
                'id' => 'creative-category-grid',
                'dataProvider' => $dataProvider,
                                'bulkActionOptions' => [
                    'gridId' => 'creative-category-grid',
                    'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px'],],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => '/creative/category',
                        'title' => function(CreativeCategory $model) {
                            return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {delete}',
                    ],
            'description:ntext',

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


