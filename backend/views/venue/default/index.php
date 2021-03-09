<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\venue\VenuePlace;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\venue\search\VenuePlaceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Venue Place');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-place-index">
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
                                'model' => VenuePlace::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'venue-place-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'venue-place-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'venue-place-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'venue-place-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px']],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'controller' => '/venue/default',
                                'title' => function (VenuePlace $model) {
                                    return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],

                            // 'id',
                            // 'sity_id',
                            // 'district_id',
                            // 'name',
                            // 'countryName',
                            [
                                'attribute' => 'country_id',
                                'value' => 'countryName',
                                'label' => Yii::t('art/guide', 'Name Country'),
                                'filter' => common\models\venue\VenueCountry::getVenueCountryList(),
                            ],
                            [
                                'attribute' => 'sityName',
                                'label' => Yii::t('art/guide', 'Name Sity'),
                            ],
                            [
                                'attribute' => 'districtSlug',
                                'label' => Yii::t('art/guide', 'Name District Slug'),
                            ],

                            'address',
                            'phone',
                            // 'phone_optional',
                            // 'email:email',
                            // 'сontact_person',
//                    'latitude',
//                    'longitude',
                            // 'description',
                            // 'created_at',
                            // 'updated_at',
                            // 'created_by',
                            // 'updated_by',

                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


