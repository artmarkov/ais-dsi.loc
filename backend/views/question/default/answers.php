<?php

use artsoft\grid\GridPageSize;
use artsoft\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

//echo '<pre>' . print_r($data, true) . '</pre>'; die();
$columns = [];
foreach ($data['attributes'] as $attribute => $label) {
    if ($attribute == 'question_users_id') {
        $columns[] = [
            'attribute' => $attribute,
            'label' => $label,
            'class' => 'artsoft\grid\columns\TitleActionColumn',
            'controller' => '/questions/default',
            'title' => function ($data) {
                return Html::a(sprintf('#%06d', $data['question_users_id']),
                    Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'view']), [
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
            },
            'buttonsTemplate' => '{update} {view} {delete}',
            'buttons' => [
                'update' => function ($url, $data, $key) {
                    return Html::a(Yii::t('art', 'Edit'),
                        Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'update']), [
                            'title' => Yii::t('art', 'Edit'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'view' => function ($url, $data, $key) {
                    return Html::a(Yii::t('art', 'View'),
                        Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'view']), [
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                },
                'delete' => function ($url, $data, $key) {
                    return Html::a(Yii::t('art', 'Delete'),
                        Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'delete']), [
                            'title' => Yii::t('art', 'Delete'),
                            'aria-label' => Yii::t('art', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                }
            ],
            'format' => 'raw',
            'options' => ['style' => 'width:250px'],
            'headerOptions' => ['class' => "grid"],
        ];
    } else {
        $columns[] = [
            'attribute' => $attribute,
            'label' => $label,
            'headerOptions' => ['class' => "grid"]
        ];
    }
}

$this->title = Yii::t('art/question', 'Answers');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="question-answers-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'question-answers-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'question-answers-grid-pjax',
            ])
            ?>
            <?= \artsoft\grid\GridView::widget([
                'id' => 'teachers-efficiency-summary',
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $data['data'],
                    'sort' => [
                        'attributes' => array_keys($data['attributes'])
                    ],
                ]),
                'columns' => $columns,
                'showFooter' => true,
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
