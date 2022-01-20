<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersLoad */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('art/guide', 'Subject Schedule'), 'url' => ['schedule/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teachers-load-create">
    <?=  $this->render('_form', [
        'model' => $model
    ]); ?>
</div>