<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\venue\VenueSity;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\venue\search\VenueSitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Sity');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-sity-index">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art', 'Add New'), ['venue/sity/create'], ['class' => 'btn btn-sm btn-default']) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => VenueSity::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'venue-sity-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'venue-sity-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'venue-sity-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'venue-sity-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px'],],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'controller' => '/venue/sity',
                                'title' => function (VenueSity $model) {
                                    return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],

//            'id',
//            'country_id',
//            'name',
                            [
                                'attribute' => 'countryName',
                                'label' => Yii::t('art/guide', 'Name Country'),
                            ],
                            'latitude',
                            'longitude',

                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


