<?php

$this->title = 'Руководство пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
    <div class="panel-heading">
        <?= $this->title ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-3">

                <?=
                \kartik\tree\TreeViewInput::widget([
                    'name' => 'kvTreeInput',
                    'id' => 'treeInput2',
                    'value' => 'true', // preselected values
                    'query' => \common\models\guidesys\HelpTree::getQueryRead(),
                    'headingOptions' => ['label' => ''],
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
                    'fontAwesome' => true,
                    'asDropdown' => false,
                    'multiple' => false,
                    'showToolbar' => false,
                    'options' => ['disabled' => false],
                ]);
                ?>
            </div>
            <div class="col-sm-9">
                <div id="cat-info">
                    <div class="box-info">Выберите элемент из списка.</div>
                </div>
            </div>
        </div>
    </div>
    <?php

    $script = <<< JS
$("#treeInput2").on('treeview:checked', function(event, key) {
       // console.log(key);
 $.ajax({
            url: '/help/guide-help/check',
            type: 'POST',
            data: {key : key},
            success: function (res) {
                console.log(res);
                $("#cat-info .box-info").html(res);
                
            },
            error: function () {
                alert('Error!!!');
            }
        });
       
});
        
       
JS;
    $this->registerJs($script, yii\web\View::POS_READY);
    ?>
