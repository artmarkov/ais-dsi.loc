<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="creative-works-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsEfficiency' => $modelsEfficiency,
        'readonly' => $readonly
    ]) ?>
</div>