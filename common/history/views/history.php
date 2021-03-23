<?php
//echo '<pre>' . print_r($provider, true) . '</pre>';

?>
<?php
use yii\widgets\Pjax;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;

$this->title = Yii::t('art', 'История изменений');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-index">
    <div class="panel">
        <div class="panel-heading">
            <?= $this->title?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">

                        <div class="col-sm-12 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'history-grid-pjax']) ?>
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'history-grid-pjax',
                    ])
                    ?>
                    <?= GridView::widget([
                        'id' => 'history-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $filterModel,
                        'bulkActionOptions' => [
                            'gridId' => 'history-grid',
                            'actions' => [],
                        ],
                        'columns' => [
                            ['attribute' => 'type', 'label' => 'Событие'],
                            ['attribute' => 'attr_label', 'label' => 'Параметр'],
                            ['attribute' => 'display_value_old', 'label' => 'Старое'],
                            ['attribute' => 'display_value_new', 'label' => 'Новое'],
                            ['attribute' => 'updated_at', 'label' => 'Дата'],
                            ['attribute' => 'updated_by_username', 'label' => 'Инициатор'],
                        ],
                    ]);

                    ?>
                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
