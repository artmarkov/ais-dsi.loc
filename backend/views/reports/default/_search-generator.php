<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\user\UserCommon;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'teachers-generator-search',
    'validateOnBlur' => false,
])
?>
    <div class="teachers-generator-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= \yii\bootstrap\Alert::widget([
                            'body' => '<i class="fa fa-info"></i> График работы преподавателей формируется исходя из Расписания занятий. 
                                                                 Академическое время переводится в астронамическое и добавляется перерыв при нагрузке более 4 часов. 
                                                                <br/> Данный график максимально приближен к Расписанию занятий.',
                            'options' => ['class' => 'alert-info'],
                        ]);
                        ?>
                        <?= $form->field($model_date, 'plan_year')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\ArtHelper::getStudyYearsList(),
                            'options' => [
                                'id' => 'plan_year',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],

                        ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>

                        <?= $form->field($model_date, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\RefBook::find('subject_type_name')->getList(),
                            'options' => [
                                'id' => 'subject_type_id',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],

                        ])->label('Тип занятий'); ?>

                        <?= $form->field($model_date, 'activity_list')->widget(DepDrop::class, [
                            'data' =>  \artsoft\helpers\RefBook::find('teachers_activity_memo', \common\models\user\UserCommon::STATUS_ACTIVE)->getList(),
                            'type' => DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'options' => [
                                'id' => 'activity_list',
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'depends' => ['subject_type_id'],
                                'url' => Url::to(['/reports/default/activity-list'])
                            ]
                        ])->label('Преподаватели по занимаемым должностям');
                        ?>

                        <?= $form->field($model_date, "update_list_flag")->checkbox()->label('Обновить список преподавателей'); ?>

                        <?= $form->field($model_date, 'limit_up_flag')->checkbox()->label('Вывести превышение нагрузки в отчет.'); ?>

                        <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

