<?php

use yii\web\JsExpression;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\service\CatalogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/service', 'Catalogs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
                <?= Html::a(Yii::t('art', 'Add New'), ['#'], ['class' => 'btn btn-sm btn-primary cat-create']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    //echo '<pre>' . print_r($data, true) . '</pre>';
                    echo \wbraganca\fancytree\FancytreeWidget::widget([
                       
                        'options' => [ 
                            'activeVisible' => true,
                            'aria' => true,
                            'autoActivate' => true,
                            'autoCollapse' => false,
                            'checkbox' => false,
                            'clickFolderMode' => 4,
                            'source' => $data,
                            'extensions' => ['dnd'], 
                            'dnd' => [
                                'preventVoidMoves' => true,
                                'preventRecursiveMoves' => true,
                                'autoExpandMS' => 400,
                                'dragStart' => new JsExpression('function(node, data) {
                           // console.log(node);
				return true;
                                }'),
                                'dragEnter' => new JsExpression('function(node, data) {
                            //console.log(node);
				return true;
                                }'),
                                'dragDrop' => new JsExpression('function(node, data) {
                                 // console.log(data, node);
				$.get("/admin/service/catalog/move", { 
                                    item: data.otherNode.data.id,
                                    action: data.hitMode,
                                    second: node.data.id 
                                }, 
                                    function() {
                                    data.otherNode.moveTo(node, data.hitMode);
                                });
                                }'),
                                ],
                            
                                 'activate' => new JsExpression('function(event, data) {
                                    var title = data.node.title;
                                    var id = data.node.data.id;
                                 // сonsole.log(data);
                                 // $("#cat-info .box-header>h3").text(title);
                                    $.get("/admin/service/catalog/update",
                                    {id: id},
                                    function(data) {
                                     $("#cat-info .box-body").html(data);
                                        });
                            }'),
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <div class="catalog-form" id ="cat-info">
                        <div class="box-header">
                            
                            <div class="box-body"></div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS

$('.cat-create').on('click', function (e) {

    e.preventDefault();
     $.get("/admin/service/catalog/create",
                
    function(data) {
     $("#cat-info .box-body").html(data);
        });
});

JS;

$this->registerJs($js);
?>