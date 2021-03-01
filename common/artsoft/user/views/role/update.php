<?php
/**
 * @var artsoft\widgets\ActiveForm $form
 * @var artsoft\models\Role $model
 */

use yii\helpers\Html;

$this->title = Yii::t('art/user', 'Update Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Roles'), 'url' => ['/user/role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-update">
    <?= $this->render('_form', compact('model')) ?>
</div>