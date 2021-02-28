<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="creative-works-update">
    <?=  $this->render('_form', compact('model')) ?>
</div>