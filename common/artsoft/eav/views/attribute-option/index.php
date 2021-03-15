<?php

use artsoft\eav\models\EavAttribute;
use artsoft\eav\models\EavAttributeOption;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EavAttributeOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/eav', 'Attribute Options');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-attribute-option-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/eav/attribute-option/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EavAttributeOption::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'eav-attribute-option-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'eav-attribute-option-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'eav-attribute-option-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'eav-attribute-option-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    ['attribute' => 'id', 'options' => ['style' => 'width:20px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'attribute' => 'value',
                        'controller' => '/eav/attribute-option',
                        'buttonsTemplate' => '{update} {delete}',
                        'title' => function (EavAttributeOption $model) {
                            return Html::a($model->value, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                    ],
                    [
                        'attribute' => 'attribute_id',
                        'value' => function (EavAttributeOption $model) {
                            return "{$model->attribute->id} - {$model->attribute->name} - {$model->attribute->label}";
                        },
                        'filter' => ArrayHelper::merge(['' => Yii::t('art', 'Not Selected')], EavAttribute::getEavAttributes()),
                        'options' => ['style' => 'width:300px']
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


