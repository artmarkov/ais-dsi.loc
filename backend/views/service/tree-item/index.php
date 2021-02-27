<?php

use artsoft\helpers\Html;
use kartik\tree\TreeView;
use common\models\service\TreeItem;
use kartik\tree\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tree Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tree-item-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?=
                    TreeView::widget([

                        'query' => TreeItem::find()->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => 'Categories'],
                        'fontAwesome' => true, // optional
                        'isAdmin' => true, // optional (toggle to enable admin mode)
                        'displayValue' => 1, // initial display value
                        'softDelete' => true, // defaults to true
                        'cacheSettings' => [
                            'enableCache' => true   // defaults to true
                        ],
                        'nodeView' => '@backend/views/service/tree-item/_form', //переопределено                 
                        'nodeAddlViews' => [
                            Module::VIEW_PART_2 => '@backend/views/service/tree-item/_treePart2',
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
 <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>

        </div>
    </div>   
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">

                    <?=
                    \kartik\tree\TreeViewInput::widget([
                        'name' => 'kvTreeInput',
                        'id' => 'treeInput2',
                        'value' => 'true', // preselected values
                        'query' => TreeItem::find()->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => 'Store'],
                        'rootOptions' => [
                            'label' => '<i class="fa fa-tree"></i>', // custom root label
                            'class' => 'text-success'
                        ],
                        'fontAwesome' => true,
                        'asDropdown' => false,
                        'multiple' => false,
                        'showToolbar' => false,
                        'options' => ['disabled' => false],
                    ]);
                    ?>
                </div>
                 <div class="col-sm-9">
                    <div class="catalog-form" id ="cat-info">
                        <div class="box-header">
                            
                            <div class="box-body">Выберите элемент из списка.</div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$script = <<< JS
$("#treeInput2").on('treeview.checked', function(event, key) {
        console.log(key);
$.get('check',{ key : key })
 
$("#cat-info .box-body").html(key);
       
});
        
       
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
