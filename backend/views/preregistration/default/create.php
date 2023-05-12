<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EntrantPreregistrations */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Preregistrations'), 'url' => ['/preregistration/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="entrant-preregistrations-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>