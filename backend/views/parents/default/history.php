<?php
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/parent', 'Parents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->parentsFullName, 'url' => ['/parents/default/update', 'id' => $model->id]];
$this->title = 'История изменений: ' . $model->parentsFullName;
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