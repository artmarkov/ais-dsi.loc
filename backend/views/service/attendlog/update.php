<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Attendlogs'), 'url' => ['service/attendlog/index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="users-attendlog-update">
    <?= $this->render('_form', compact('model')) ?>
</div>