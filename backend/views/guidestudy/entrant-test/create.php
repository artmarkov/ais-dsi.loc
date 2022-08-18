<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\GuideEntrantTest */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Tests'), 'url' => ['/guidestudy/entrant-test']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="guide-entrant-test-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>