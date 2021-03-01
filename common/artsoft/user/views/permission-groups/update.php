<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\models\AuthItemGroup $model
 */

$this->title = Yii::t('art/user', 'Update Permission Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Permission Groups'), 'url' => ['/user/permission-groups/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-groups-update">
    <?= $this->render('_form', compact('model')) ?>
</div>
