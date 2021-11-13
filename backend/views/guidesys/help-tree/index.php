<?php

use artsoft\models\User;
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Руководство пользователя';
$this->params['breadcrumbs'][] = $this->title;
$readonly = (User::hasPermission('editHelp') || Yii::$app->user->isSuperadmin) ? false : true;
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
        <div class="panel-heading">
            <?= $this->title ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?=
                    TreeView::widget([
                        'mainTemplate' => $mainTemplate,
                        'query' => \common\models\guidesys\HelpTree::find()->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => ''],
                        'fontAwesome' => true, // optional
                        'isAdmin' => !$readonly, // optional (toggle to enable admin mode)
                        'showFormButtons' => !$readonly,
                        'toolbar' => [
                            TreeView::BTN_CREATE =>      ['alwaysDisabled' => $readonly],
                            TreeView::BTN_CREATE_ROOT => ['alwaysDisabled' => $readonly],
                            TreeView::BTN_REMOVE =>      ['alwaysDisabled' => $readonly],
                            TreeView::BTN_MOVE_UP =>     ['alwaysDisabled' => $readonly],
                            TreeView::BTN_MOVE_DOWN =>   ['alwaysDisabled' => $readonly],
                            TreeView::BTN_MOVE_LEFT =>   ['alwaysDisabled' => $readonly],
                            TreeView::BTN_MOVE_RIGHT =>  ['alwaysDisabled' => $readonly],
                            TreeView::BTN_REFRESH =>     ['alwaysDisabled' => $readonly],
                        ],
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
