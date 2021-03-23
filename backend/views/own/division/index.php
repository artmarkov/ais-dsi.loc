<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\own\Division;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Division');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="division-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?php
                        /* Uncomment this to activate GridQuickLinks */
                        /* echo GridQuickLinks::widget([
                            'model' => Division::className(),
                            'searchModel' => $searchModel,
                        ])*/
                        ?>
                    </div>

                    <div class="col-sm-6 text-right">
                        <?=  GridPageSize::widget(['pjaxId' => 'division-grid-pjax']) ?>
                    </div>
                </div>

                <?php
                Pjax::begin([
                    'id' => 'division-grid-pjax',
                ])
                ?>

                <?=
                GridView::widget([
                    'id' => 'division-grid',
                    'dataProvider' => $dataProvider,
                    'bulkActionOptions' => [
                        'gridId' => 'division-grid',
                        'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                    ],
                    'columns' => [
                        ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                        [
                            'class' => 'artsoft\grid\columns\TitleActionColumn',
                            'options' => ['style' => 'width:300px'],
                            'attribute' => 'name',
                            'controller' => '/own/division',
                            'title' => function (Division $model) {
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
</div>


