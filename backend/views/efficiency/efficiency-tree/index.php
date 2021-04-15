<?php

use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Дерево показателей';
$this->params['breadcrumbs'][] = $this->title;
$mainTemplate = <<< HTML
<div class="panel">
    <div class="row">
        <div class="col-sm-12">
            {wrapper}
        </div>
    </div>
</div>
<div class="panel">
    <div class="row">
        <div class="col-sm-12">
            {detail}
        </div>
    </div>
</div>
HTML;
?>
<div class="tree-item-index">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?=
                    TreeView::widget([
                        'mainTemplate' => $mainTemplate,
                        'query' => \common\models\efficiency\EfficiencyTree::find()->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => 'Дерево показателей'],
                        'fontAwesome' => true, // optional
                        'isAdmin' => true, // optional (toggle to enable admin mode)
                        'displayValue' => 1, // initial display value
                        'softDelete' => true, // defaults to true
                        'cacheSettings' => [
                            'enableCache' => true   // defaults to true
                        ],
                        'nodeView' => '@backend/views/efficiency/efficiency-tree/_form', //переопределено
                        'nodeAddlViews' => [
                            Module::VIEW_PART_2 => '@backend/views/efficiency/efficiency-tree/_treePart2',
                        ],
                        'rootOptions' => [
                            'label' => '<i class="fa fa-tree"></i>', // custom root label
                            'class' => 'text-default'
                        ],
                        'nodeActions' => [
                            Module::NODE_MANAGE => Url::to(['/treemanager/node/manage']),
                            Module::NODE_SAVE => Url::to(['/treemanager/node/save']),
                            Module::NODE_REMOVE => Url::to(['/treemanager/node/remove']),
                            Module::NODE_MOVE => Url::to(['/treemanager/node/move']),
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
