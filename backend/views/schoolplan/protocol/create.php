<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schoolplan-protocol-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>