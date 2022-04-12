<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\activities\ActivitiesPlan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Activities Plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-plan-index">
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
                        'model' => ActivitiesPlan::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'activities-plan-grid-pjax']) ?>
                </div>
            </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'activities-plan-grid-pjax',
                    ])
                    ?>

                    <?= 
                    GridView::widget([
                        'id' => 'activities-plan-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'activities-plan-grid',
                            'actions' => [ Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/activities-plan/default',
                                'title' => function(ActivitiesPlan $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

            'id',
            'author_id',
            'name',
            'datetime_in:datetime',
            'datetime_out:datetime',
            // 'places',
            // 'auditory_id',
            // 'department_list',
            // 'teachers_list',
            // 'category_id',
            // 'form_partic',
            // 'partic_price',
            // 'visit_flag',
            // 'visit_content:ntext',
            // 'important_flag',
            // 'region_partners:ntext',
            // 'site_url:url',
            // 'site_media',
            // 'description:ntext',
            // 'rider:ntext',
            // 'result:ntext',
            // 'num_users',
            // 'num_winners',
            // 'num_visitors',
            // 'created_at',
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'version',

                ],
            ]);
            ?>

                    <?php Pjax::end() ?>
        </div>
    </div>
</div>


