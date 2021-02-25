<?php

use artsoft\widgets\DateRangePicker;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\models\Session;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var artsoft\user\models\search\UserVisitLogSearch $searchModel
 */

$this->title = Yii::t('art/user', 'Sessions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-sm-12">
        <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
    </div>
</div>
<div class="user-session-index">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= GridPageSize::widget(['pjaxId' => 'user-session-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'user-session-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'user-session-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'user-session-grid',
                    'actions' => [
                        Url::to(['bulk-delete']) => Yii::t('art/user', 'Destroy sessions'),
                    ],
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('art', 'ID'),
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/user/session',
                        'title' => function ($model) {
                            return $model->id;
                        },
                        'buttonsTemplate' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return $model->getStatus() != 'current' ? Html::a(Yii::t('art/user', 'Destroy session'), $url, [
                                    'title' => Yii::t('art/user', 'Destroy session'),
                                    'aria-label' => Yii::t('art/user', 'Destroy session'),
                                    'data-confirm' => Yii::t('art/user', 'Are you sure you want to destroy this session?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]) : null;
                            },
                        ],
                    ],
                    [
                        'attribute' => 'username',
                        'label' => Yii::t('art', 'Login'),
                        'value' => function ($model) {
                            return $model->getUsername();
                        },
                    ],
                    [
                        'attribute' => 'ip',
                        'value' => function ($model) {
                            return $model->getIP();
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->getStatusLabel($model->getStatus());
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'run_at',
                        'value' => function (Session $model) {
                            return $model->getRunAt() ? Yii::$app->formatter->asDateTime($model->getRunAt()) . '<br />(' . \yii\timeago\TimeAgo::widget(
                                    [
                                        'timestamp' => $model->getRunAt(),
                                        'language' => Yii::$app->art->getDisplayLanguageShortcode(Yii::$app->language)
                                    ]) . ')' : '';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'expire',
                        'value' => function (Session $model) {
                            return Yii::$app->formatter->asDateTime($model->expire);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

