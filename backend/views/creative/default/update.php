<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */
/* @var $readonly */


$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="creative-works-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsEfficiency' => $modelsEfficiency,
        'readonly' => $readonly
    ]) ?>
</div>