<?php

use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\efficiency\search\TeachersEfficiencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Efficiencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">
Сводная таблица
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <!--                        --><?php //echo '<pre>' . print_r($root, true) . '</pre>'; ?>
                    <!--                        --><?php //echo '<pre>' . print_r($tree, true) . '</pre>'; ?>
<!--                    --><?php //echo '<pre>' . print_r($models, true) . '</pre>'; die()?>

                    <?php
//                            $stake = \common\models\teachers\Teachers::find()->joinWith('teachersActivity')
//                                ->where(['teachers.id' => $model['teachers_id']])
//                                ->andWhere(['!=', 'teachers_activity.work_id', 3])
//                                ->select(['teachers.id','stake_id'])->column();
//                      print_r($stake);
//                            $mon = Yii::$app->formatter->asDate($model['date_in'], 'php:m');
//                            $mon = Yii::$app->formatter->asDate($model['date_in'], 'php:Y');
                    $res = [];
                    foreach ($models as $model) {
                        $res[$model['teachers_id']][$tree[$model['efficiency_id']]] = isset($res[$model['teachers_id']][$tree[$model['efficiency_id']]]) ? $res[$model['teachers_id']][$tree[$model['efficiency_id']]] + $model['bonus'] : $model['bonus'];
                        $res[$model['teachers_id']]['total'] = isset($res[$model['teachers_id']]['total']) ? $res[$model['teachers_id']]['total'] + $model['bonus'] : $model['bonus'];
                    }
                    $data = [];
                    foreach (\artsoft\helpers\RefBook::find('teachers_fio', \common\models\user\UserCommon::STATUS_ACTIVE)->getList() as $id => $name) {
                        $data[$id] = $res[$id] ?? ['total' => null];
                        $data[$id]['id'] = $id;
                        $data[$id]['name'] = $name;
                        $data[$id]['stake'] = 22200;
                    }
//                    echo '<pre>' . print_r($data, true) . '</pre>';;die();
                    $dataProvider = new \yii\data\ArrayDataProvider([
                        'allModels' => $data,
                        'sort' => [
                            'attributes' => array_merge(['id', 'name', 'total'], array_keys($root))
                        ],
                        'pagination' => false,
                    ]);
                    $columns = [];
                    $columns[] = ['class' => 'yii\grid\SerialColumn'];
                   // $columns[] = ['attribute' => 'id', 'label' => 'ИД', 'headerOptions' => ['style' => "white-space: normal; vertical-align: top;"]];
                    $columns[] = ['attribute' => 'name', 'label' => 'ФИО', 'options' => ['style' => 'width:350px'], 'headerOptions' => ['style' => "white-space: normal; vertical-align: top;"]];

                    foreach ($root as $id => $name) {
                        $columns[] = ['attribute' => $id, 'label' => $name, 'headerOptions' => ['style' => "white-space: normal; vertical-align: top;"]];
                    }
                    $columns[] = [
                        'attribute' => 'total',
                        'label' => 'Надбавка %',
                        'footer' => 'Итого:',
                    ];
                    $columns[] = [
                        'attribute' => 'total',
                        'label' => 'Сумма руб.',
                        'value' => function ($data) {
                            return number_format($data['total'] * $data['stake'] * 0.01, 2);
                        },
                        'footer' => 444,
                    ];
                   // $columns[] = ['class' => 'yii\grid\ActionColumn', 'options' => ['style' => 'width:350px']];
                    ?>
                    <?= GridView::widget([
                        'id' => 'teachers-efficiency-summary',
                        'dataProvider' => $dataProvider,
                        'columns' => $columns,
                        'showFooter' => true,
                    ]);
                    ?>
                </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \yii\helpers\Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel',
                        ['#'],
                        [
                            'class' => 'btn btn-default ',
                        ]
                    ); ?>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
.grid-view tbody tr td {
     height: 30px; 
}
CSS;

$this->registerCss($css);
?>