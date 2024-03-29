<?php

use artsoft\widgets\DateRangePicker;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\models\Request;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\logs\models\search\UserVisitLogSearch $searchModel
 */

$this->title = Yii::t('art/user', 'Requests');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Visit Log'), 'url' => ['/logs/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="user-requests-index">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= GridPageSize::widget(['pjaxId' => 'user-requests-grid-pjax']) ?>
                    </div>
                </div>
                <?php
                Pjax::begin([
                    'id' => 'user-requests-grid-pjax',
                ])
                ?>

                <?=
                GridView::widget([
                    'id' => 'user-requests-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'bulkActionOptions' => [
                        'gridId' => 'user-requests-grid',
                        'actions' => [
                            Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),
                        ],
                    ],
                    'columns' => [
                        ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                        [
                            'attribute' => 'created_at',
                            'class' => 'artsoft\grid\columns\TitleActionColumn',
                            'controller' => '/logs/request',
                            'title' => function ($model) {
                                return $model->created_at;
                            },
                            'buttonsTemplate' => '{delete}',

                        ],
                        [
                            'attribute' => 'user_id',
                            'label' => Yii::t('art', 'Login'),
                            'format' => 'raw',
                            'value' => function ($model) {
                                return \artsoft\models\User::findOne($model->user_id)->username;
                            }
                        ],
                        'url',
                        'post',
                        'time',
                        'mem_usage_mb',
                        'http_status',
                    ],
                ]);
                ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>


<?php
DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'created_at',
    'format' => 'YYYY-MM-DD H:mm',
    'opens' => 'left',
])
?>