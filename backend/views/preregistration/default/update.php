<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EntrantPreregistrations */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Preregistrations'), 'url' => ['/preregistration/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="entrant-preregistrations-update">
    <?= $this->render('_form', compact('model')) ?>
</div>