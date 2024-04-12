<?php

use artsoft\grid\GridPageSize;
use artsoft\helpers\Html;
use common\models\question\QuestionAttribute;
use yii\helpers\Url;
use yii\widgets\Pjax;

//echo '<pre>' . print_r($data, true) . '</pre>'; die();
$columns = [];

$columns[] = ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'checkboxOptions' => function ($data) {
    return ['value' => $data['question_users_id']];
},
    'visible' => \artsoft\Art::isBackend(),
];

foreach ($data['attributes'] as $attribute => $label) {
    $columns[] = [
        'attribute' => $attribute,
        'label' => $label,
        'headerOptions' => ['class' => "grid"],
        'format' => (isset($data['types'][$attribute]) && $data['types'][$attribute] == QuestionAttribute::TYPE_FILE) ? 'image' : 'html',
        'hiddenFromExport'=> (isset($data['types'][$attribute]) && $data['types'][$attribute] == QuestionAttribute::TYPE_FILE) ? true : false,
    ];
}
$columns[] = [
    'class' => 'kartik\grid\ActionColumn',

    'controller' => '/question/default',
    'template' => '{view} {update} {delete}',
    'buttons' => [
        'update' => function ($url, $data, $key) {
            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'update']), [
                    'title' => Yii::t('art', 'Edit'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]
            );
        },
        'view' => function ($url, $data, $key) {
            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'view']), [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        },
        'delete' => function ($url, $data, $key) {
            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                Url::to(['question/default/answers', 'id' => $data['question_id'], 'objectId' => $data['question_users_id'], 'mode' => 'delete']), [
                    'title' => Yii::t('art', 'Delete'),
                    'aria-label' => Yii::t('art', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        }
    ],
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => 'kartik-sheet-style'],

];


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
                'id' => 'question-answers-grid',
                'bulkActionOptions' => [
                    'gridId' => 'question-answers-grid',
                    'actions' => [
                        Url::to(['users-bulk-activate']) => 'Перевести в статус "Просмотрено"',
                        Url::to(['users-bulk-deactivate']) => 'Перевести в статус "В работе"',
                        Url::to(['users-bulk-delete']) => Yii::t('yii', 'Delete'),
                    ]
                ],
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

<?php
$css = <<<CSS
.question-answers-grid img {
   width: 100px;
    height: 100px;
    border-radius: 10px;
    border: 1px solid #3b5876;
    padding: 3px;
    vertical-align: middle;
}
CSS;
$this->registerCss($css);
?>
