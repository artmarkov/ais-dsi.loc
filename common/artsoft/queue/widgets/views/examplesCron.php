<?php

use artsoft\helpers\Html;
use artsoft\grid\GridView;

$this->title = Yii::t('art/queue', 'Examples Cron Expression.');
?>

<div class="cron-help-widget">

    <div class="row">
        <div class="col-sm-12">
            <h5 class="page-title"><?= Html::encode($this->title) ?></h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <?= GridView::widget([
                'id' => 'examplesCron',
                'dataProvider' => $dataProvider,
                'layout' => '{items}',
                'tableOptions' => ['class' => 'table table-hover'],
                'showHeader' => false,
                'showFooter' => false,
                'columns' => [
                    [
                        'attribute' => 'cron',
                        'options' => ['style' => 'width:150px'],
                    ],
                    'text',
                ],
            ]);
            ?>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
        
    #examplesCron.grid-view tbody tr td {
        height: auto !important; 
    }
        
CSS;

$this->registerCss($css);
?>