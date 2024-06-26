<?php
$user = \common\models\teachers\Teachers::findOne($model->id)->getFullName();

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['/teachers/default/view', 'id' => $model->id]];
$this->title = 'История изменений: ' . $user;
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