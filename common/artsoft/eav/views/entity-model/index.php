<?php

use artsoft\eav\models\EavEntityModel;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel artsoft\eav\search\EavEntityModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/eav', 'Entity Models');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-entity-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/eav/entity-model/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EavEntityModel::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'eav-entity-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'eav-entity-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'eav-entity-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'eav-entity-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    ['attribute' => 'id', 'options' => ['style' => 'width:20px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'attribute' => 'entity_name',
                        'controller' => '/eav/entity-model',
                        'buttonsTemplate' => '{update} {delete}',
                        'title' => function (EavEntityModel $model) {
                            return Html::a($model->entity_name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                    ],
                    'entity_model',
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


