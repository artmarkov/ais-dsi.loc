<?php

use artsoft\widgets\DateRangePicker;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\UserVisitLogSearch $searchModel
 */

$this->title = Yii::t('art/user', 'Visit Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
    </div>
</div>
<div class="user-visit-log-index">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'user-visit-log-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'user-visit-log-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'user-visit-log-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'user-visit-log-grid',
                    'actions' => [
                        Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),
                    ],
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'user_id',
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/user/visit-log',
                        'title' => function ($model) {
                            return Html::a(@$model->user->username,
                                ['view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{view} {delete}',
                    ],
                    'language',
                    'os',
                    'browser',
                    array(
                        'attribute' => 'ip',
                        'value' => function ($model) {
                            return Html::a($model->ip,
                                "http://ipinfo.io/" . $model->ip,
                                ["target" => "_blank"]);
                        },
                        'format' => 'raw',
                    ),
                    [
                        'attribute' => 'visit_time',
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                        'value' => function ($model) {
                            return $model->visitDatetime;
                        },
                        'options' => ['style' => 'width:270px'],
                    ],
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
    'attribute' => 'visit_time',
    'format' => 'DD.MM.YYYY H:mm',
    'opens' => 'left',
])
?>
