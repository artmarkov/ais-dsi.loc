<?php
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['indivplan/default/index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['indivplan/default/update', 'id' => $model->id]];
$this->title = 'История изменений: ' . sprintf('#%06d', $model->id);
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