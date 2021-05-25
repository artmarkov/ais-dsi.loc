<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\efficiency\TeachersEfficiency;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\efficiency\search\TeachersEfficiencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Efficiencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">

        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
<?php echo '<pre>' . print_r($root, true) . '</pre>';?>
<?php echo '<pre>' . print_r($tree, true) . '</pre>';?>
<?php //echo '<pre>' . print_r($models, true) . '</pre>';?>

<?php
$res = [];
foreach ($models as $model){
//    print_r($tree[$model['efficiency_id']]);
    $mon = Yii::$app->formatter->asDate($model['date_in'], 'php:m');
    $mon = Yii::$app->formatter->asDate($model['date_in'], 'php:Y');
    $res[$model['teachers_id']][$tree[$model['efficiency_id']]] = isset($res[$model['teachers_id']][$tree[$model['efficiency_id']]]) ? $res[$model['teachers_id']][$tree[$model['efficiency_id']]] + $model['bonus'] : $model['bonus'];
    $res[$model['teachers_id']]['id'] = $model['teachers_id'];
    $res[$model['teachers_id']]['name'] = \artsoft\helpers\RefBook::find('teachers_fullname')->getValue($model['teachers_id']);
}

$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $res,
    'sort' => [
        'attributes' => array_merge(['id', 'name'], array_keys($root))
    ],
    'pagination' => [
        'pageSize' => \Yii::$app->request->cookies->getValue('_grid_page_size', 20),
    ],
]);
$columns = [];
foreach ($root as $id => $name) {
    $columns[] = ['attribute' => $id, 'label' => $name, 'headerOptions' => ['style'=> "white-space: normal;"]];
    }
?>
<?= GridView::widget([
    'id' => 'history-grid',
    'dataProvider' => $dataProvider,
//    'headerOptions' => ['class' => 'text-center'],
    // 'filterModel' => $filterModel,
    'columns' => array_merge([
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'label' => 'ИД'],
        ['attribute' => 'name', 'label' => 'ФИО']],
        $columns),
]);
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
