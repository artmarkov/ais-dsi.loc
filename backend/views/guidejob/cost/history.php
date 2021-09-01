<?php
$this->params['breadcrumbs'][] = ['label' => $model->getDirectionName() . ' (' . $model->getStakeName() . ')', 'url' => ['/guidejob/cost/update', 'id' => $model->id]];
$this->title = 'История изменений: ' . $model->getDirectionName() . ' (' . $model->getStakeName() . ')';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="history-index">
    <div class="panel">
        <div class="panel-heading">
            <?= $this->title ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= \common\widgets\history\HistoryWidget::widget(['data' => $data]); ?>
                </div>
            </div>
        </div>
    </div>
</div>