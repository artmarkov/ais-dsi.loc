<?php
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student', 'Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->studentsFullName, 'url' => ['/students/default/update', 'id' => $model->id]];
$this->title = 'История изменений: ' . $model->studentsFullName;
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