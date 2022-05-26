<?php

use artsoft\widgets\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\service\UsersCardLog;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\service\search\UsersCardLogsearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Users Card Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-card-log-index">
    <div class="panel">
        <div class="panel-heading">
            Журнал СКУД
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => UsersCardLog::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'users-card-log-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'users-card-log-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'users-card-log-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'users-card-log-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (UsersCardLog $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'name',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:350px'],
                                'controller' => '/service/sigur',
                                'title' => function (UsersCardLog $model) {
                                    return Html::a($model->name, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{view} {delete}',
                            ],
                            'position',
                            'datetime',
                            'key_hex',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'evtype_code',
                                'optionsArray' => [
                                    [1, 'Проход', 'success'],
                                    [2, 'Запрет', 'danger'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'dir_code',
                                'optionsArray' => [
                                    [1, 'Выход', 'info'],
                                    [2, 'Вход', 'primary'],
                                    [3, 'Неизвестное', 'warning'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'attribute' => 'deny_reason',
                                'filter' =>  UsersCardLog::DENY_REASON,
                                'value' => function (UsersCardLog $model) {
                                    return (int)$model->deny_reason != null ? UsersCardLog::DENY_REASON[(int)$model->deny_reason] : '';
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'user_common_id',
                                'value' => function (UsersCardLog $model) {
                                    return (int)$model->user_common_id != null ? '<span class="label label-success">Да</span>' : '<span class="label label-danger">Нет</span>';
                                },
                                'label' => 'Зарегистрирован в АИС',
                                'format' => 'raw',
                                'contentOptions' => ['style' => "text-align:center; vertical-align: middle;"]
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

<?php
DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'datetime',
    'format' => 'YYYY-MM-DD HH:mm',
    'opens' => 'left',
])
?>
