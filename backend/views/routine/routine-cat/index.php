
<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subject\SubjectCategory;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\routine\RoutineCat;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/routine', 'Routine Cats');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routine'), 'url' => ['routine/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routine-cat-index">
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
                              'model' => RoutineCat::className(),
                              'searchModel' => $searchModel,
                              ]) */
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <? //= GridPageSize::widget(['pjaxId' => 'routine-cat-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'routine-cat-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'routine-cat-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'routine-cat-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'controller' => '/routine/routine-cat',
                                'title' => function (RoutineCat $model) {
                                    return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            [
                                'attribute' => 'color',
                                'value' => function(RoutineCat $model){
                                    return '<div style="background-color:' . $model->color . '">&nbsp;</div>';
                                },
                                'format' => 'html',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'plan_flag',
                                'optionsArray' => [
                                    [RoutineCat::FLAG_ACTIVE, Yii::t('art', 'Yes'), 'primary'],
                                    [RoutineCat::FLAG_INACTIVE, Yii::t('art', 'No'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


