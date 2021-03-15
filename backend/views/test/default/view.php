<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Customers'), 'url' => ['test/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">

<div class="panel">
    <div class="panel-heading">
        <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::viewButtons($model) ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
'first_name',
'last_name',
                ],
            ])?>
                </div>
            </div>
        </div>
    </div>
</div>
