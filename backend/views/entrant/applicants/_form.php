<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;
use common\models\entrant\EntrantComm;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
use common\models\students\Student;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\Entrant */
/* @var $model common\models\entrant\EntrantComm */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model */
/* @var $modelsMembers */
/* @var $modelsTest */

$js = <<<JS
    function toggleEntrant(value) {
      if (value === '1'){
          $('.dec1').show();
          $('.dec2').hide();
      } else if (value === '2') {
          $('.dec1').hide();
          $('.dec2').show();
      } else  {
          $('.dec1').hide();
          $('.dec2').hide();
      }
    }
    toggleEntrant($('input[name="Entrant[decision_id]"]:checked').val());
    $('input[name="Entrant[decision_id]"]').click(function(){
       toggleEntrant($(this).val());
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

//$readonly = $model->decision_id != 0 ? true : $readonly;
$readonlyMarks = $model->status == 2 || $model->status == 0 ? true : $readonly;
$readonlyBase = ($model->status != 0 && !$model->isNewRecord) || !User::hasPermission('fullEntrantAccess') ? true : $readonly;

?>

<div class="applicants-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'applicants-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка поступающего
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <span class="pull-right">
            <?= Html::a('<i class="fa fa-user-o" aria-hidden="true"></i> Открыть в новом окне',
                ['/students/default/view', 'id' => $model->student_id],
                [
                    'target' => '_blank',
                    'class' => 'btn btn-info',
                ]); ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if (!$model->isNewRecord) {
                        echo Html::activeHiddenInput($model, 'id');
                    }
                    ?>
                    <?= $form->field($model, 'student_id')->widget(\kartik\select2\Select2::class, [
                        'data' => Student::getStudentList(),
                        'options' => [
                            'disabled' => $model->student_id ? true : false,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'comm_id')->widget(\kartik\select2\Select2::class, [
                        'data' => EntrantComm::getComList(),
                        'options' => [
                            'id' => 'comm_id',
                            'disabled' => $model->comm_id ? true : false,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'group_id')->widget(DepDrop::class, [
                        'data' => \common\models\entrant\Entrant::getCommGroupList($model->comm_id),
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'disabled' => $readonly /*$readonlyBase*/,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['comm_id'],
                            'url' => Url::to(['/entrant/default/groups'])
                        ]
                    ]);
                    ?>

                    <?= $form->field($model, 'subject_list')->widget(DepDrop::class, [
                        'data' => \common\models\entrant\Entrant::getCommSubjectList($model->comm_id),
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'disabled' => $readonlyBase,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'depends' => ['comm_id'],
                            'url' => Url::to(['/entrant/default/subject'])
                        ]
                    ]);
                    ?>

                    <?= $form->field($model, 'last_experience')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'remark')->textarea(['rows' => 3, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'status')->dropDownList(Entrant::getStatusList(), ['disabled' => !Yii::$app->user->isSuperadmin]) ?>

                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 10, // the maximum times, an element can be added (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsMembers[0],
                    'formId' => 'applicants-form',
                    'formFields' => [
                        'entrant_id',
                        'members_id',
                        'mark_rem',
                    ],
                ]); ?>
                <?php
                $modelComm = EntrantComm::findOne($model->comm_id);
                $guideTests = $modelComm->getTests($model->group_id);
                ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Результаты испытаний
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="container-items"><!-- widgetBody -->
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">№</th>
                                        <th rowspan="2" class="text-center">Члены комиссии</th>
                                        <th colspan="<?= count($guideTests) ?>" class="text-center">Оценки за
                                            испытания
                                        </th>
                                        <th rowspan="2" class="text-center">Комментарий</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($guideTests as $index => $item): ?>
                                            <th class="text-center"><?= $item['name'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsMembers as $index => $modelMembers): ?>
                                        <?php
                                        // necessary for update action.
                                        if (!$modelMembers->isNewRecord) {
                                            echo Html::activeHiddenInput($modelMembers, "[{$index}]id");
                                        }
                                        ?>
                                        <tr class="item">
                                            <td>
                                                <span class="panel-title-activities"><?= ($index + 1) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                echo Html::activeHiddenInput($modelMembers, "[{$index}]members_id");
                                                ?>
                                                <div class="col-sm-12">
                                                 <span class="panel-title-activities">
                                                     <?php
                                                     if (isset(\artsoft\models\User::findOne($modelMembers->members_id)->userCommon)) {
                                                         echo \artsoft\models\User::findOne($modelMembers->members_id)->userCommon->getFullName();
                                                     }
                                                     ?>
                                                 </span>
                                                </div>

                                            </td>
                                            <?= $this->render('_form-test', [
                                                'form' => $form,
                                                'index' => $index,
                                                'model' => $model,
                                                'modelsTest' => $modelsTest[$index],
                                                'readonly' => $readonlyMarks,
                                            ]) ?>
                                            <td>
                                                <?php
                                                $field = $form->field($modelMembers, "[{$index}]mark_rem");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextarea($modelMembers, "[{$index}]mark_rem", ['rows' => 3, 'class' => 'form-control', 'disabled' => $readonlyMarks]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <?php if (\artsoft\models\User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend()): ?>
                                        <tfoot>
                                        <tr class="info">
                                            <td colspan="2" align="right">
                                                <b>Средняя оценка:</b>
                                            </td>
                                            <td colspan="<?= count($guideTests) + 1 ?>">
                                                <b><?= $model->getEntrantMidMark(); ?></b>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="form-group btn-group">
                                <?php if (\artsoft\models\User::hasPermission('adminEntrant')): ?>
                                    <?php $Url = Yii::$app->request->resolve(); ?>
                                    <?= \artsoft\helpers\Html::a('<i class="fa fa-hourglass-start" aria-hidden="true"></i> Начать испытания',
                                        [$Url[0], 'id' => $Url[1]['id'], 'objectId' => $Url[1]['objectId'], 'mode' => 'activate'], [
                                            'class' => 'btn btn-success btn-md',
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'disabled' => !$readonlyMarks
                                        ]
                                    ) ?>
                                    <?= \yii\helpers\Html::a('<i class="fa fa-hourglass-end" aria-hidden="true"></i> Завершить испытания',
                                        [$Url[0], 'id' => $Url[1]['id'], 'objectId' => $Url[1]['objectId'], 'mode' => 'deactivate'], [
                                            'class' => 'btn btn-warning btn-md',
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'disabled' => $readonlyMarks
                                        ]
                                    ); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php DynamicFormWidget::end(); ?>
                <?php if (\artsoft\models\User::hasPermission('fullEntrantAccess') && \artsoft\Art::isBackend()): ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Решение комиссии
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= $form->field($model, 'decision_id')->radioList(Entrant::getDecisionList(), ['itemOptions' => ['disabled' => $readonly]]) ?>
                                </div>
                                <div class="dec2 col-sm-12">
                                    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="dec1 col-sm-12">
                                    <?= $form->field($model, "programm_id")->widget(\kartik\select2\Select2::class, [
                                        'data' => RefBook::find('education_programm_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                                        'options' => [
                                            'id' => 'programm_id',
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art/studyplan', 'Select Education Programm...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ]
                                    ]);
                                    ?>
                                    <?= $form->field($model, "subject_id")->widget(\kartik\select2\Select2::class, [
                                        'data' => \common\models\subject\Subject::getSubjectByCategory(1000),
                                        'options' => [
                                            'id' => 'subject_id',
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art/guide', 'Select Education Specializations...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ]
                                    ]);
                                    ?>

                                    <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                                        'options' => [
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art/guide', 'Select Course...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ]);
                                    ?>

                                    <?= $form->field($model, 'subject_form_id')->widget(\kartik\select2\Select2::class, [
                                        'data' => RefBook::find('subject_form_name',  \common\models\subject\SubjectForm::STATUS_ACTIVE)->getList(),
                                        'options' => [
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art', 'Select...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ])->label(Yii::t('art/guide', 'Subject Form'));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if (\artsoft\Art::isBackend() && \artsoft\models\User::hasPermission('adminEntrant')): ?>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="form-group btn-group">
                                    <?php $Url = Yii::$app->request->resolve(); ?>
                                    <?= \artsoft\helpers\Html::a('<i class="fa fa-id-card-o" aria-hidden="true"></i> Сформировать учебный план',
                                        [$Url[0], 'id' => $Url[1]['id'], 'objectId' => $Url[1]['objectId'], 'mode' => 'make_studyplan'], [
                                            'class' => 'btn btn-info btn-md',
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'disabled' => $model->decision_id != 1
                                        ]
                                    ) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
