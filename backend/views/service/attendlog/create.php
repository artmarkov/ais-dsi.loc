<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */
/* @var $readonly */
/* @var $modelsDependency */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Attendlogs'), 'url' => ['service/attendlog']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="users-attendlog-create">    
    <?=  $this->render('_form', [
        'model' => $model,
        'modelsDependency' => $modelsDependency,
        'readonly' => $readonly
    ]) ?>
</div>