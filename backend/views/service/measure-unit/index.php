<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\service\MeasureUnit;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide','Measure Unit');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="measure-unit-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/service/measure-unit/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php 
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => measure-unit::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?//=  GridPageSize::widget(['pjaxId' => 'measure-unit-grid-pjax']) ?>
                </div>
            </div>

            <?php 
            Pjax::begin([
                'id' => 'measure-unit-grid-pjax',
            ])
            ?>

            <?= 
            GridView::widget([
                'id' => 'measure-unit-grid',
                'dataProvider' => $dataProvider,
                                'bulkActionOptions' => [
                    'gridId' => 'measure-unit-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => 'service/measure-unit',
                        'title' => function(MeasureUnit $model) {
                            return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {delete}',
                    ],

//            'id',
//            'name',
            'slug',

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


