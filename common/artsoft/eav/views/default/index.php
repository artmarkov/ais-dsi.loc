<?php

use artsoft\eav\assets\EavAsset;
use artsoft\helpers\Html;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */

$this->title = Yii::t('art/eav', 'Custom Fields');
$this->params['breadcrumbs'][] = Yii::t('art/eav', 'EAV');

EavAsset::register($this);
?>
<div class="eav-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>

            <?= Alert::widget([
                'options' => ['class' => 'alert-primary eav-link-alert'],
                'body' => '<span class="glyphicon glyphicon-refresh glyphicon-spin"></span>',
            ]) ?>

            <?= Alert::widget([
                'options' => ['class' => 'alert-danger eav-link-alert'],
                'body' => Yii::t('art/eav', 'An error occurred during saving EAV attributes!'),
            ]) ?>

            <?= Alert::widget([
                'options' => ['class' => 'alert-info eav-link-alert'],
                'body' => Yii::t('art', 'The changes have been saved.'),
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="pull-left" style="margin-right: 10px;">
                        <label class="control-label" for="entityModel">Model: </label>
                        <?= Html::dropDownList('entityModel', null, $entityModels, ['id' => 'entityModel', 'class' => 'form-control']) ?>
                    </div>
                    <div class="pull-left" style="display: none;">
                        <label class="control-label pull-left" for="entityCategory">
                            <?= Yii::t('art', 'Category') ?>:
                        </label>
                        <div class="eav-categories-wrapper">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-body eav-attributes eav-available">
                    <h4><span class="label label-primary"><?= Yii::t('art/eav', 'Available Attributes') ?></span></h4>
                    <div class="content">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-body eav-attributes eav-selected">
                    <h4><span class="label label-primary"><?= Yii::t('art/eav', 'Selected Attributes') ?></span></h4>
                    <div class="content">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


