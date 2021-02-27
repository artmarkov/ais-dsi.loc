<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Level;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers','Level');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/teachers/level/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php 
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Level::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?//=  GridPageSize::widget(['pjaxId' => 'level-grid-pjax']) ?>
                </div>
            </div>

            <?php 
            Pjax::begin([
                'id' => 'level-grid-pjax',
            ])
            ?>

            <?= 
            GridView::widget([
                'id' => 'level-grid',
                'dataProvider' => $dataProvider,
                                'bulkActionOptions' => [
                    'gridId' => 'level-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                       'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => '/teachers/level',
                        'title' => function(Level $model) {
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


