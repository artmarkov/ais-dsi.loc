<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeCategory */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['creative/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="creative-category-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>