<?php

use artsoft\helpers\ButtonHelper;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;
use common\models\entrant\EntrantComm;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\Entrant */
/* @var $model common\models\entrant\EntrantComm */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model */
/* @var $modelsMembers */
/* @var $modelsTest */

?>

<div class="applicants-form-import">

    <?php
    $form = ActiveForm::begin([
        'id' => 'applicants-form-import',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Пакетная обработка поступающих
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'file')->fileInput() ?>

                </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= ButtonHelper::saveButton('submitAction', 'saveexit', 'Отправить данные формы'); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
