<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use yii\helpers\Url;
use common\models\education\LessonMark;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */
/* @var $modelSchoolplan common\models\schoolplan\Schoolplan */
/* @var $form artsoft\widgets\ActiveForm */

$mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE,LessonMark::MARK,LessonMark::OFFSET_NONOFFSET,LessonMark::REASON_ABSENCE]);

?>

    <div class="protocol-form">

        <?php
        $form = ActiveForm::begin([
            'id' => 'protocol-form',
            'validateOnBlur' => false,
        ])
        ?>

        <div class="panel">
            <div class="panel-heading">
                Аттестационная карточка
                <?php if (!$model->isNewRecord): ?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <?php
                if ($model->isNewRecord) {
                    echo \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info-circle"></i> Для групповых занятий возможно добавление задания из репертуарного плана. Для индивидуальных занятий форма ограничена только выбором учеников. 
                            В дальнейшем задание можно добавить в режиме редактирования аттестационной карточки.',
                        'options' => ['class' => 'alert-info'],
                    ]);
                }
                ?>
                <div class="col-sm-12">
                    <div class="row">
                        <?php
                        if ($model->isNewRecord) {
                            echo Html::activeHiddenInput($model, "schoolplan_id");
                        }
                        ?>
                        <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                            'data' => $modelSchoolplan->getTeachersListForProtocol(),
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

                        <?= $form->field($model, 'studyplan_subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                            'data' => $modelSchoolplan->getStudyplanSubjectListByTeachers($model->teachers_id),
                            'options' => [
                                'id' => 'studyplan_subject_id',
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => $model->isNewRecord ? true : false,
                            ],
                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['teachers_id'],
                                'url' => Url::to(['/schoolplan/default/studyplan', 'id' => $model->schoolplan_id])
                            ],

                        ]);
                        ?>
                        <?php if (!($model->schoolplan->protocol_subject_vid_id == 1000 && $model->isNewRecord)): ?>
                            <?= $form->field($model, 'thematicFlag')->checkbox(['disabled' => $readonly]) ?>
                        <?php endif; ?>
                        <div id="thematicItemsList">
                            <?= $form->field($model, 'thematic_items_list')->widget(\kartik\depdrop\DepDrop::className(), [
                                'data' => $modelSchoolplan->getThematicItemsByStudyplanSubject($model->studyplan_subject_id),
                                'options' => [

                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                    'multiple' => true,
                                ],
                                'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                'pluginOptions' => [
                                    'depends' => ['studyplan_subject_id'],
                                    'url' => Url::to(['/schoolplan/default/studyplan-thematic', 'id' => $model->schoolplan_id])
                                ]
                            ]);
                            ?>
                        </div>
                        <?php if (!$model->isNewRecord): ?>
                        <div id="taskTicket">
                            <?= $form->field($model, 'task_ticket')->textInput(['disabled' => $readonly]) ?>
                        </div>
                            <?= $form->field($model, 'lesson_mark_id')->widget(\kartik\select2\Select2::class, [
                                'data' => $mark_list,
                                'showToggleAll' => false,
                                'options' => [
                                    'disabled' => (\artsoft\Art::isFrontend() && !$model->schoolplan->isProtocolMembers()) ? true : $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                ],
                            ]);
                            ?>
                            <?= $form->field($model, 'resume')->textarea(['rows' => 3, 'maxlength' => true, 'disabled' => /*(\artsoft\Art::isFrontend() && !$model->schoolplan->isProtocolSigner()) ? true :*/ $readonly]) ?>
                        <?php endif; ?>
                    </div>

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
 // Взять из репертуарного плана
     if($('input[type=checkbox][name="SchoolplanProtocol[thematicFlag]"]').prop('checked')) {
       $('#thematicItemsList').show();
       $('#taskTicket').hide();
       } else {
       $('#taskTicket').show();
       $('#thematicItemsList').hide();
       }
    $('input[type=checkbox][name="SchoolplanProtocol[thematicFlag]"]').click(function() {
       if($(this).prop('checked')) {
       $('#thematicItemsList').show();
       $('#taskTicket').hide();
       } else {
       $('#taskTicket').show();
       $('#thematicItemsList').hide();
       }
           
     });
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);