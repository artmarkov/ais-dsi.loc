<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\SchoolplanProtocolItems;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanProtocolItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Schoolplan Protocol Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schoolplan-protocol-items-index">
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
                        'model' => SchoolplanProtocolItems::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'schoolplan-protocol-items-grid-pjax']) ?>
                </div>
            </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'schoolplan-protocol-items-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'schoolplan-protocol-items-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'schoolplan-protocol-items-grid',
                            'actions' => [ Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/schoolplan-protocol-items/default',
                                'title' => function(SchoolplanProtocolItems $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

            'id',
            'schoolplan_protocol_id',
            'studyplan_subject_id',
            'thematic_items_list',
            'lesson_progress_id',
             'winner_id',
             'resume',
             'status_exe',
             'status_sign',
             'signer_id',

                ],
            ]);
            ?>

                    <?php Pjax::end() ?>
        </div>
    </div>
</div>


