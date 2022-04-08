<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Cards'), 'url' => ['service/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="users-card-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>