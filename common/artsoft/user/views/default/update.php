<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\models\User $model
 */
$this->title = Yii::t('art/user', 'Update User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-update">
    <?= $this->render('_form', compact('model')) ?>
</div>