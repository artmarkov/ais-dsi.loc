<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Document */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['info/document/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-create">    
    <?=  $this->render('_form', compact('model', 'readonly')) ?>
</div>