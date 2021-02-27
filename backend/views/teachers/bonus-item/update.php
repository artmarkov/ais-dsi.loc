<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\BonusItem */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers Bonus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');

?>
<div class="bonus-item-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>