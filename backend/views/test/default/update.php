<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $modelCustomer->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Customers'), 'url' => ['test/default/index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-update">
    <?=  $this->render('_form',[
        'modelCustomer' => $modelCustomer,
        'modelsAddress' => $modelsAddress ]);
    ?>
</div>