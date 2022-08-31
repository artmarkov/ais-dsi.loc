<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\Entrant */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrants'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="entrant-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>