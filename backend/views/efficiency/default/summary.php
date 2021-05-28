<?php

use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use common\models\efficiency\EfficiencyTree;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = 'Сводная таблица';

$columns = [];
$columns[] = ['class' => 'yii\grid\SerialColumn'];
$columns[] = [
    'attribute' => 'name',
    'label' => 'Фамилия И.О.',
    'value' => function ($data) {
        return Html::a($data['name'],
            Url::to(['efficiency/default/details', 'id' => $data['id'], 'date_in' => $data['date_in'], 'date_out' => $data['date_out']]), [
                'data-method' => 'post',
                'data-pjax' => '0',
            ]);
    },
    'format' => 'raw',
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => "grid"]
];

foreach ($data['root'] as $id => $name) {
    $columns[] = ['attribute' => $id, 'label' => $name, 'headerOptions' => ['class' => "grid"]];
}
$columns[] = [
    'attribute' => 'stake',
    'label' => $data['attributes']['stake'],
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total',
    'label' => $data['attributes']['total'],
    'footer' => 'Итого:',
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total_sum',
    'label' => $data['attributes']['total_sum'],
    'value' => function ($data) {
        return number_format($data['total_sum'], 2);
    },
    'footer' => number_format($data['all_summ'], 2),
    'headerOptions' => ['class' => "grid"]
];
?>
<?php
$form = ActiveForm::begin([
    'id' => 'teachers-efficiency-summary',
//    'action' => '/admin/efficiency/default/summary',
    'validateOnBlur' => false,
])
?>
    <div class="teachers-efficiency-summary">
        <div class="panel">
            <div class="panel-heading">
                Сводная таблица
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $form->field($model_date, "date_in")->widget(DatePicker::class)->label('Дата начала периода'); ?>
                        <?= $form->field($model_date, "date_out")->widget(DatePicker::class)->label('Дата окончания периода'); ?>
                        <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary']); ?>
                        <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>

                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Результаты запроса
                    </div>
                    <div class="panel-body">
                        <?= GridView::widget([
                            'id' => 'teachers-efficiency-summary',
                            'dataProvider' => new \yii\data\ArrayDataProvider([
                                'allModels' => $data['data'],
                                'sort' => [
                                    'attributes' => array_keys($data['attributes'] + $data['root'])
                                ],
                                'pagination' => false,
                            ]),
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