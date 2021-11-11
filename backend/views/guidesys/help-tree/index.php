<?php

use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Руководство пользователя';
$this->params['breadcrumbs'][] = $this->title;
$mainTemplate = <<< HTML
<div class="panel">
    <div class="row">
        <div class="col-sm-3">
            {wrapper}
        </div>
        <div class="col-sm-9">
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
                        'query' => \common\models\guidesys\HelpTree::find()->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => 'Руководство пользователя'],
                        'fontAwesome' => false, // optional
                        'isAdmin' => true, // optional (toggle to enable admin mode)
                        'displayValue' => 1, // initial display value
                        'softDelete' => true, // defaults to true
                        'childNodeIconOptions' => ['class' => ''],
                        'defaultParentNodeIcon' => '',
                        'defaultParentNodeOpenIcon' => '',
                        'defaultChildNodeIcon' => '',
                        'childNodeIconOptions' => ['class' => ''],
                        'parentNodeIconOptions' => ['class' => ''],
                        'rootOptions' => [
                            'label' => '',
                            'class' => 'text-default'
                        ],
                        'cacheSettings' => [
                            'enableCache' => true
                        ],
                        'nodeView' => '@backend/views/guidesys/help-tree/_form', //переопределено
                        'nodeAddlViews' => [
                            Module::VIEW_PART_2 => '@backend/views/guidesys/help-tree/_treePart2',
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
