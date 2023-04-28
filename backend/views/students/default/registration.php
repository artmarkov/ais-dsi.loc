<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model common\models\forms\RegistrationForm */

use artsoft\widgets\ActiveForm;
use common\models\user\UserCommon;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

$this->title = Yii::t('art/student', 'Students');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Регистрация';

?>
<div class="panel">
    <?php $form = ActiveForm::begin([
        'id' => 'form-registration',
        'options' => ['autocomplete' => 'off'],
        'validateOnBlur' => false,
        'fieldConfig' => [
        ],
    ]);
    ?>
    <div class="panel-heading">
        Регистрация ученика
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                Информация об ученике
            </div>
            <div class="panel-body">
                <div class="panel">
                    <div class="panel-body">
                        <?= $form->field($model, 'student_last_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        <?= $form->field($model, 'student_first_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        <?= $form->field($model, 'student_middle_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124])->hint('Важно: Поле необходимо заполнить как в документе. При отсутствии Отчества заполнение не требуется.') ?>
                        <?= $form->field($model, 'student_gender')->dropDownList(UserCommon::getGenderList()/*, ['disabled' => $readonly]*/) ?>
                        <?= $form->field($model, 'student_birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')]); ?>
                        <?= $form->field($model, 'student_snils')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Документ
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'student_sert_name')->dropDownList(\common\models\students\Student::STUDENT_DOC, [
                                    /*'disabled' => $readonly,*/
                                    'options' => [
                                        'birth_cert' => ['selected' => true]
                                    ]
                                ]) ?>
                                <?= $form->field($model, 'student_sert_series')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'student_sert_num')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'student_sert_organ')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'student_sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()/*, ['disabled' => $readonly]*/); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Информация о родителе(официальном представителе)
            </div>
            <div class="panel-body">
                <div class="panel">
                    <div class="panel-body">
                        <?= $form->field($model, 'relation_id')->dropDownList(\common\models\guidesys\UserRelation::getRelationList(), [
                            'prompt' => Yii::t('art/student', 'Select Relations...'),
                        ])->label(Yii::t('art/student', 'Relation'));
                        ?>
                        <?= $form->field($model, 'parent_last_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        <?= $form->field($model, 'parent_first_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        <?= $form->field($model, 'parent_middle_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124])->hint('Важно: Поле необходимо заполнить как в документе. При отсутствии Отчества заполнение не требуется.') ?>
                        <?= $form->field($model, 'parent_gender')->dropDownList(UserCommon::getGenderList()) ?>
                        <?= $form->field($model, 'parent_birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')]); ?>
                        <?= $form->field($model, 'parent_snils')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Документ
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'parent_sert_name')->dropDownList(\common\models\parents\Parents::PARENT_DOC, [
                                    /*'disabled' => $readonly,*/
                                    'options' => [
                                        'birth_cert' => ['selected' => true]
                                    ]
                                ]) ?>
                                <?= $form->field($model, 'parent_sert_series')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'parent_sert_num')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'parent_sert_organ')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'parent_sert_code')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'parent_sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()/*, ['disabled' => $readonly]*/); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Информация для связи
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'phone')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                <?= $form->field($model, 'phone_optional')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                <?= $form->field($model, 'email')->textInput(['maxlength' => 124]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group btn-group">
            <div class="form-group btn-group">
                <?php
                echo \artsoft\helpers\ButtonHelper::exitButton('/admin/students/default/index', 'btn-md');
                echo \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'save', 'Save', 'btn-md');
                 ?>
                <?= \artsoft\helpers\Html::submitButton(
                    '<i class="fa fa-floppy-o" aria-hidden="true"></i> Перейти в карточку',
                    [
                        'class' => 'btn btn-info btn-md',
                        'name' => 'submitAction',
                        'value' => 'students',
                    ]
                );
                ?>
                <?= \artsoft\helpers\Html::submitButton(
                    '<i class="fa fa-floppy-o" aria-hidden="true"></i> Принять на обучение',
                    [
                        'class' => 'btn btn-info btn-md',
                        'name' => 'submitAction',
                        'value' => 'studyplan',
                    ]
                );
                ?>
                <?= \artsoft\helpers\Html::submitButton(
                    '<i class="fa fa-floppy-o" aria-hidden="true"></i> Отправить на экзамены',
                    [
                        'class' => 'btn btn-info btn-md',
                        'name' => 'submitAction',
                        'value' => 'examination',
                    ]
                );
                ?>
                <?= \artsoft\helpers\Html::submitButton(
                    '<i class="fa fa-floppy-o" aria-hidden="true"></i> Предварительная запись',
                    [
                        'class' => 'btn btn-warning btn-md',
                        'name' => 'submitAction',
                        'value' => 'preregostration',
                    ]
                );
                ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>


