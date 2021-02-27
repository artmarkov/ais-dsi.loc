<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\Measure;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use yii\helpers\ArrayHelper;
use artsoft\models\User;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Measures';
$this->params['breadcrumbs'][] = $this->title;

$model = new Measure;
//echo '<pre>' . print_r($model->getEavAttributesRules($model)) . '</pre>';
?>
<div class="measure-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/measure/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Measure::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'measure-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'measure-grid-pjax',
            ])
            ?>

            <?=
                 GridView::widget([
                'id' => 'measure-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'measure-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                     'columns' => ArrayHelper::merge(
                         [
                             ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                             [
                                 'class' => 'artsoft\grid\columns\TitleActionColumn',
                                 'attribute' => 'name',
                                 'options' => ['style' => 'width:200px'],
                                 'controller' => '/measure',
                                 'title' => function (Measure $model) {
                                         return Html::a($model->name, ['/measure/view',  'id' => $model->id], ['data-pjax' => 0]);
                                 },
                             ],

//                           'id',
//                           'name',
                             'abbr',
                             'category_id',
                         ],
                         $model->getEavAttributesIndexList($model)
                     ),
            ]);
            ?>


            <?php Pjax::end() ?>
        </div>
    </div>
</div>


