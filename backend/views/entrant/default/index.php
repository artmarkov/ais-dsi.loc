<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\EntrantComm;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantCommSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Entrant Comms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-comm-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EntrantComm::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-comm-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-comm-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-comm-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-comm-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'name',
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/entrant/default',
                        'title' => function (EntrantComm $model) {
                            return Html::a($model->name, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {view} {delete}',
                    ],
                    [
                        'attribute' => 'division_id',
                        'value' => function (EntrantComm $model) {
                            return \artsoft\helpers\RefBook::find('division_name')->getValue($model->division_id);
                        }
                        ,
                        'label' => Yii::t('art/guide', 'Name Division'),
                        'filter' => \artsoft\helpers\RefBook::find('division_name')->getList()
                    ],
                    [
                        'attribute' => 'plan_year',
                        'filter' => \artsoft\helpers\ArtHelper::getStudyYearsList(),
                        'value' => function (EntrantComm $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    'timestamp_in:date',
                    'timestamp_out:date',
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


