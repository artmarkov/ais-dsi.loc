<?php

use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */
/* @var $root */

$this->title = Yii::t('art/guide', 'Efficiencies');
$this->params['breadcrumbs'][] = $this->title;

$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $data['data'],
    'sort' => [
        'attributes' => array_merge(['id', 'name', 'total', 'total_sum', 'stake'], array_keys($root))
    ],
    'pagination' => false,
]);

$columns = [];
$columns[] = ['class' => 'yii\grid\SerialColumn'];
$columns[] = [
    'attribute' => 'name',
    'label' => 'Фамилия И.О.',
    'value' => function ($data) {
        return Html::a($data['name'], ['details', 'id' => $data['id'], 'date_in' => $data['date_in'], 'date_out' => $data['date_out']], ['data-pjax' => 0]);
    },
    'format' => 'raw',
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => "grid"]
];

foreach ($root as $id => $name) {
    $columns[] = ['attribute' => $id, 'label' => $name, 'headerOptions' => ['class' => "grid"]];
}
$columns[] = [
    'attribute' => 'stake',
    'label' => 'Ставка руб.',
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total',
    'label' => 'Надбавка %',
    'footer' => 'Итого:',
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total_sum',
    'label' => 'Сумма руб.',
    'value' => function ($data) {
        return number_format($data['total_sum'], 2);
    },
    'footer' => number_format($data['all_summ'], 2),
    'headerOptions' => ['class' => "grid"]
];
?>
<?php
$form = ActiveForm::begin()
?>
    <div class="teachers-efficiency-summary">
        <div class="panel">
            <div class="panel-heading">
                Сводная таблица
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $form->field($model_date, "date_in")->widget(DatePicker::class, [
                            'type' => DatePicker::TYPE_INPUT,
                            'options' => ['placeholder' => ''],
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd.MM.yyyy',
                                'autoclose' => true,
                            ]
                        ])->label('Дата начала периода'); ?>
                        <?= $form->field($model_date, "date_out")->widget(DatePicker::class, [
                            'type' => DatePicker::TYPE_INPUT,
                            'options' => ['placeholder' => ''],
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd.MM.yyyy',
                                'autoclose' => true,
                            ]
                        ])->label('Дата окончания периода'); ?>
                        <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary']); ?>
                        <?= \yii\helpers\Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel',
                            ['#'],
                            [
                                'class' => 'btn btn-default ',
                            ]
                        ); ?>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Результаты запроса
                    </div>
                    <div class="panel-body">
                        <?= GridView::widget([
                            'id' => 'teachers-efficiency-summary',
                            'dataProvider' => $dataProvider,
                            'columns' => $columns,
                            'showFooter' => true,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php
$css = <<<CSS
.teachers-efficiency-summary .grid {
    white-space: normal; 
    vertical-align: top;
}
 .grid-view tbody tr td {
     height: 30px; 
}

CSS;

$this->registerCss($css);
?>