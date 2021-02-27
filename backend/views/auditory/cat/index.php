<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\auditory\AuditoryCat;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide','Auditory Cat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory'), 'url' => ['auditory/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditory-cat-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/auditory/cat/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => AuditoryCat::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'auditory-cat-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'auditory-cat-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'auditory-cat-grid',
                'dataProvider' => $dataProvider,
                'bulkActionOptions' => [
                    'gridId' => 'auditory-cat-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px'],],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => '/auditory/cat',
                        'title' => function (AuditoryCat $model) {
                            return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {delete}',
                    ],

                    // 'id',
                    // 'name',
                    'description',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'study_flag',
                        'options' => ['style' => 'width:60px']
                    ],
                    // 'study_flag',
                    'order',

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


