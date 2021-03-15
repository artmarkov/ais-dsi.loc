<?php

use artsoft\eav\models\EavAttribute;
use artsoft\eav\models\EavAttributeType;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel artsoft\eav\models\search\EavAttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/eav', 'Attributes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-attribute-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/eav/attribute/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EavAttribute::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'eav-attribute-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'eav-attribute-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'eav-attribute-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'eav-attribute-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    ['attribute' => 'id', 'options' => ['style' => 'width:20px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'attribute' => 'label',
                        'controller' => '/eav/attribute',
                        'buttonsTemplate' => '{update} {delete}',
                        'title' => function (EavAttribute $model) {
                            return Html::a($model->label, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                    ],
                    'name',
                    [
                        'attribute' => 'type_id',
                        'value' => function (EavAttribute $model) {
                            return $model->eavType->name;
                        },
                        'filter' => ArrayHelper::merge(['' => Yii::t('art', 'Not Selected')], EavAttributeType::getAttributeTypes()),
                        'options' => []
                    ],
                    //'default_value',
                    'description',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'required',
                        'options' => ['style' => 'width:80px']
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'visible',
                        'options' => ['style' => 'width:80px']
                    ],

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


