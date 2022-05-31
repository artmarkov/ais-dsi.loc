<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\ButtonHelper;
use common\models\question\QuestionAttribute;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $modelAttributes \common\models\question\QuestionAttribute */
/* @var $modelQuestion */
/* @var $model */
/* @var $readonly */
//print_r($modelAttributes); die();

$options = [];

?>

<div class="answers-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'answers-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            Карточка формы
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $modelQuestion->name ?>
                </div>
                <div class="panel-body">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> ' . $modelQuestion->description,
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php foreach ($modelAttributes as $id => $item): ?>
                                <?php
                                $modelOptions = \common\models\question\QuestionOptions::find()->select(['id', 'name'])->where(['=', 'attribute_id', $item['id']])->asArray()->all();
                                $options = ArrayHelper::map($modelOptions, 'id', 'name');
//                        print_r($options);
                                switch ($item['type_id']) {
                                    case QuestionAttribute::TYPE_STRING :
                                    case QuestionAttribute::TYPE_EMAIL :
                                        echo $form->field($model, $item['name'])->textInput(['maxlength' => true])->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_TEXT :
                                        echo $form->field($model, $item['name'])->textarea(['rows' => 4])->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_DATE :
                                        echo $form->field($model, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonly])->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_DATETIME :
                                        echo $form->field($model, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->widget(DateTimePicker::class, ['disabled' => $readonly])->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_PHONE :
                                        echo $form->field($model, $item['name'])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput()->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_RADIOLIST :
                                        echo $form->field($model, $item['name'])->radioList($options)->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_CHECKLIST :
                                        echo $form->field($model, $item['name'])->checkboxList($options)->label($item['label'])->hint($item['hint']);
                                        break;
                                    case QuestionAttribute::TYPE_FILE :
                                        echo $form->field($model, $item['name'])->fileInput()->label($item['label'])->hint($item['hint']);
                                        break;
                                    default:
                                        echo $form->field($model, $item['name'])->textInput(['maxlength' => true]);
                                }
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?php $result = ButtonHelper::exitButton();
                $result .= ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit');
                $result .= ButtonHelper::saveButton();
                echo $result;
                ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
