<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocolItems */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol Items'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schoolplan-protocol-items-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>