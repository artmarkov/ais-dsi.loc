<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\efficiency\TeachersEfficiency */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="teachers-efficiency-update">
    <?= $this->render('_form', compact('model')) ?>
</div>