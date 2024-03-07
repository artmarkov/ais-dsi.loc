<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanPerform */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = (\artsoft\Art::isBackend() || (\artsoft\Art::isFrontend() && in_array($model->status_sign, [1, 2]))) ? false : $readonly;
?>

    <div class="perform-form">

        <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
                'inputOptions' => ['readonly' => $readonly]
            ],
            'id' => 'perform-form',
            'validateOnBlur' => false,
        ])
        ?>

        <div class="panel">
            <div class="panel-heading">
                Карточка выполнения мероприятия
                <?php if (!$model->isNewRecord): ?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <?php if ($model->isNewRecord): ?>
                        <?= \yii\bootstrap\Alert::widget([
                            'body' => '<i class="fa fa-info"></i> Возможность загрузки файлов появится после первого сохранения формы.',
                            'options' => ['class' => 'alert-info'],
                        ]);
                        ?>
                    <?php endif; ?>
                    <div class="row">
                        <?php
//                        if ($model->isNewRecord) { // открыл для смены статусов, чтобы работал Yii::$app->request->post()
                            echo Html::activeHiddenInput($model, "schoolplan_id");
//                        }
                        ?>
                        <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                            'data' => $model->schoolplan->getExecutorsList(),
                            'options' => [
                                'id' => 'teachers_id',
                                'disabled' => /*$model->schoolplan->isExecutors() ? true :*/ $readonly,
                                'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/teachers', 'Teacher'));
                        ?>

                        <?= $form->field($model, 'studyplan_id')->widget(\kartik\depdrop\DepDrop::class, [
                            'data' => \common\models\teachers\TeachersLoadStudyplanView::getStudyplanListByTeachers($model->teachers_id, $plan_year),
                            'options' => [
                                'id' => 'studyplan_id',
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['teachers_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/schoolplan/default/studyplan-perform', 'plan_year' => $plan_year])
                            ],

                        ]);
                        ?>
                        <?= $form->field($model, 'studyplan_subject_id')->widget(\kartik\depdrop\DepDrop::className(), [
                            'data' => \common\models\studyplan\Studyplan::getStudyplanSubjectListByStudyplan($model->studyplan_id),
                            'options' => [
                                'id' => 'studyplan_subject_id',
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['studyplan_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/schoolplan/default/studyplan-subject-perform'])
                            ]
                        ]);
                        ?>
                        <?= $form->field($model, 'thematic_items_list')->widget(\kartik\depdrop\DepDrop::className(), [
                            'data' => \common\models\schoolplan\SchoolplanPerform::getThematicItemsByStudyplanSubject($model->studyplan_subject_id),
                            'options' => [

                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => true,
                            ],
                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['studyplan_subject_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/schoolplan/default/studyplan-thematic-perform'])
                            ]
                        ]);
                        ?>

                        <?= $form->field($model, 'lesson_mark_id')->widget(\kartik\select2\Select2::class, [
                            'data' => RefBook::find('lesson_mark')->getList(),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]);
                        ?>

                        <?= $form->field($model, 'winner_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\schoolplan\SchoolplanPerform::getWinnerList(),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],

                        ]);
                        ?>
                        <?= $form->field($model, 'resume')->textarea(['rows' => 3, 'maxlength' => true, 'disabled' => $readonly]) ?>
                    </div>
                    <?php if (!$model->isNewRecord): ?>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">Загруженные материалы(сканы диплома, грамоты)</label>
                                </div>
                                <div class="col-sm-9">
                                    <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true]/*, 'pluginOptions' => ['theme' => 'explorer'], 'disabled' => $readonly*/]) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="row">
                        <?= $form->field($model, 'status_exe')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\schoolplan\SchoolplanPerform::getStatusExeList(),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],
                        ]);
                        ?>
                <?php if (Yii::$app->settings->get('mailing.schoolplan_perform_doc')): ?>
                        <?= $form->field($model, 'status_sign')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\schoolplan\SchoolplanPerform::getStatusSignList(),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => true,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],

                        ]);
                        ?>

                        <?= $form->field($model, 'signer_id')->widget(\kartik\select2\Select2::class, [
                            'data' => User::getUsersByIds(User::getUsersByRole('department,administrator')),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],

                        ]);
                        ?>
                        <?php endif; ?>
                    </div>
                    <?php if (Yii::$app->settings->get('mailing.schoolplan_perform_doc') && !$model->isNewRecord): ?>
                        <?php if (\artsoft\Art::isBackend() || (\artsoft\Art::isFrontend() && $model->isSigner())): ?>
                            <?php if ($model->status_sign == 1): ?>
                                <div class="row">
                                    <hr>
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'admin_flag')->checkbox(/*['disabled' => $readonly]*/)->label('Добавить сообщение') ?>
                                        <div id="send_admin_message">
                                            <?= $form->field($model, 'admin_message')->textInput()->hint('Введите сообщение для участника мароприятия и нажмите "Отправить на доработку"') ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="form-group btn-group">
                                    <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Согласовать', ['class' => 'btn btn-sm btn-success', 'name' => 'submitAction', 'value' => 'approve', 'disabled' => $model->status_sign == 1]); ?>
                                    <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить на доработку', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'modif', 'disabled' => $model->status_sign == 3]); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="form-group btn-group">
                                    <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Отправить на согласование', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'send_approve', 'disabled' => in_array($model->status_sign, [0, 3]) && $model->isAuthor() ? false : true]); ?>
                                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Внести изменения', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'make_changes', 'disabled' => in_array($model->status_sign, [1, 2]) && $model->isAuthor() ? false : true]); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : (\artsoft\Art::isBackend() ? \artsoft\helpers\ButtonHelper::viewButtons($model) : \artsoft\helpers\ButtonHelper::exitButton()); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$js = <<<JS
     // Показ модуля админа
    $('input[type=checkbox][name="SchoolplanPerform[admin_flag]"]').prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
    $('input[type=checkbox][name="SchoolplanPerform[admin_flag]"]').click(function() {
       $(this).prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
     });
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
