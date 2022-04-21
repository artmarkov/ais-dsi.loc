<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\service\ServiceCardView;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\service\search\UsersCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Users Cards');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],

    [
        'options' => ['style' => 'width:30px'],
        'attribute' => 'user_common_id',
        'label' => Yii::t('art', 'ID'),
        'value' => function (ServiceCardView $model) {
            return sprintf('#%06d', $model->user_common_id);
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'user_name',
        'value' => function (ServiceCardView $model) {

            $user = \common\models\user\UserCommon::findOne($model->user_common_id);
            return Html::a($model->user_name, $user->getRelatedUrl(), ['title' => 'Перейти в реестр', 'target' => '_blank', 'data-pjax' => 0]);
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'user_category',
        'filter' => \common\models\user\UserCommon::getUserCategoryList(),
        'value' => function (ServiceCardView $model) {
            return $model->user_category_name;
        },
        'format' => 'raw'
    ],
    'phone',
    'phone_optional',
    'email',
    'key_hex',
    'timestamp_deny',
//                    'mode_main',
//                    'mode_list',
//                    'status',
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                    Url::to(['/service/default/create', 'user_common_id' => $model->user_common_id]), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                    Url::to(['/service/default/update', 'id' => $model->users_card_id]), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/service/default/delete', 'id' => $model->users_card_id]), [
                        'title' => Yii::t('art', 'Delete'),
                        'aria-label' => Yii::t('art', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
        'visibleButtons' => [
            'create' => function ($model) {
                return $model->users_card_id == null;
            },
            'delete' => function ($model) {
                return $model->users_card_id !== null;
            },
            'update' => function ($model) {
                return $model->users_card_id !== null;
            }
        ]
    ],
];
?>
<div class="users-card-index">
    <div class="panel">
        <div class="panel-heading">
            Журнал пропусков
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
                            <?= GridPageSize::widget(['pjaxId' => 'users-card-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'users-card-grid-pjax',
                    ])
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= GridView::widget([
                                'id' => 'users-card-grid',
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'columns' => $columns,
                                'beforeHeader' => [
                                    [
                                        'columns' => [
                                            ['content' => 'Пользователь', 'options' => ['colspan' => 6, 'class' => 'text-center warning']],
                                            ['content' => 'Пропуск', 'options' => ['colspan' => 4, 'class' => 'text-center info']],
                                        ],
                                        'options' => ['class' => 'skip-export'] // remove this row from export
                                    ]
                                ],
                            ]);
                            ?>

                        </div>
                    </div>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


