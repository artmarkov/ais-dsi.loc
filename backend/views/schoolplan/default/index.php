<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\Schoolplan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'School Plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schoolplan-plan-index">
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
                    <?= GridPageSize::widget(['pjaxId' => 'schoolplan-plan-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'schoolplan-plan-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'schoolplan-plan-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'schoolplan-plan-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (Schoolplan $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/schoolplan/default',
                        'title' => function (Schoolplan $model) {
                            return Html::a($model->name, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {view} {delete}',
                    ],

                    'datetime_in:datetime',
                    'datetime_out:datetime',
                    'category_id',
                    'places',
                    'auditory_id',
//                    'department_list',
//                    'executors_list',
                    // 'form_partic',
                    // 'partic_price',
                    // 'visit_poss',
                    // 'visit_content:ntext',
                    // 'important_event',
                    // 'region_partners:ntext',
                    // 'site_url:url',
                    // 'site_media',
                    'description:ntext',
                    'rider:ntext',
                    'result:ntext',
//                    'num_users',
//                    'num_winners',
//                    'num_visitors',

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


