<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Customers'), 'url' => ['test/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-create">    
    <?=  $this->render('_form',[
        'modelCustomer' => $modelCustomer,
        'modelsAddress' => $modelsAddress ]);
    ?>
</div>