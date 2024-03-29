<?php

use artsoft\models\User;
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Каталог файлов';
$isAdmin = \artsoft\Art::isBackend();
$readonly = (User::hasPermission('viewCatalog') && \artsoft\Art::isFrontend()) ? true : false;
$allowNewRoots = (User::hasPermission('allowNewRootsCatalog') || Yii::$app->user->isSuperadmin) ? true : false;
$showFormButtons = (User::hasPermission('viewCatalog') || Yii::$app->user->isSuperadmin) ? true : false;
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
                        'query' => \common\models\info\FilesCatalog::getQueryEdit(),
                        'headingOptions' => ['label' => ''],
                        'fontAwesome' => true, // optional
                        'allowNewRoots' => $allowNewRoots,
                        'isAdmin' => $isAdmin, // optional (toggle to enable admin mode)
                        'showFormButtons' => $showFormButtons,
                        'toolbar' => [
                            TreeView::BTN_CREATE =>      ['alwaysDisabled' => false],
                            TreeView::BTN_CREATE_ROOT => ['alwaysDisabled' => $readonly],
                            TreeView::BTN_REMOVE =>      ['alwaysDisabled' => false],
                            TreeView::BTN_MOVE_UP =>     ['alwaysDisabled' => false],
                            TreeView::BTN_MOVE_DOWN =>   ['alwaysDisabled' => false],
                            TreeView::BTN_MOVE_LEFT =>   ['alwaysDisabled' => false],
                            TreeView::BTN_MOVE_RIGHT =>  ['alwaysDisabled' => false],
                            TreeView::BTN_REFRESH =>     ['alwaysDisabled' => false],
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
                        'nodeView' => '@backend/views/info/catalog/_form', //переопределено
                        'nodeAddlViews' => [
                            Module::VIEW_PART_2 => '@backend/views/info/catalog/_treePart2',
                            Module::VIEW_PART_3 => '@backend/views/info/catalog/_treePart3',
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
