<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */
/* @var $readonly */
/* @var $modelsDependency */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Attendlogs'), 'url' => ['service/attendlog/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="users-attendlog-update">
    <?=  $this->render('_form', [
        'model' => $model,
        'modelsDependency' => $modelsDependency,
        'readonly' => $readonly
    ]) ?>
</div>