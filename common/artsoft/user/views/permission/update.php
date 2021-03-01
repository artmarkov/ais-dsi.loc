<?php
/**
 * @var artsoft\widgets\ActiveForm $form
 * @var artsoft\models\Permission $model
 */

use yii\helpers\Html;

$this->title = Yii::t('art/user', 'Update Permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Permissions'), 'url' => ['/user/permission/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="permission-update">
    <?= $this->render('_form', compact('model')) ?>
</div>