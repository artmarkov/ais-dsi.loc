<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Work;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Work');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-index">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art', 'Add New'), ['/teachers/work/create'], ['class' => 'btn btn-sm btn-default']) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => Work::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <? //=  GridPageSize::widget(['pjaxId' => 'work-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'work-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'work-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'work-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'controller' => '/teachers/work',
                                'title' => function (Work $model) {
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
</div>


