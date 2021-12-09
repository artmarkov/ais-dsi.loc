<?php

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;
use kartik\depdrop\DepDrop;
use kartik\popover\PopoverX;
use artsoft\helpers\Html;
use common\models\teachers\Teachers;

/* @var $readonly */
/* @var $modelsSubject */

$JSSubmit = <<<EOF
    function(event, val, form) {
    location.reload();
//    console.log(event);
    }
EOF;
?>
<div class="panel">
    <div class="panel-heading">
        Расписание занятий
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Расписание занятий
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Предмет</th>
                                        <th class="text-center">Час/нед</th>
                                        <th class="text-center">Группа</th>
                                        <th class="text-center">Расписание занятий</th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                        <tr class="item">
                                            <td>
                                                <?= RefBook::find('subject_memo_2')->getValue($modelSubject->id ?? null) ?>
                                            </td>
                                            <td>
                                                <?= $modelSubject->week_time; ?>
                                            </td>
                                            <td>
                                                <?php if (!$modelSubject->isIndividual()): ?>
                                                    <?= Editable::widget([
                                                        'id' => $modelSubject->id,
                                                        'name' => "sect_id[{$modelSubject->id}]",
                                                        'value' => $modelSubject->getSubjectSectStudyplan()->id,
                                                        'header' => 'группу',
                                                        'displayValueConfig' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                        'format' => Editable::FORMAT_LINK,
                                                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                        'data' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                        'options' => ['class' => 'form-control'],
                                                        'formOptions' => [
                                                            'action' => Url::toRoute([
                                                                '/subjectsect/default/set-group',
                                                                'studyplan_subject_id' => $modelSubject->id ?? null
                                                            ]),
                                                        ],
                                                        'pluginEvents' => [
                                                            "editableSubmit" => new JsExpression($JSSubmit),
                                                        ],
                                                        'dataAttributes' => ['sect_id' => $modelSubject->getSubjectSectStudyplan()->id]
                                                    ]);
                                                    ?>
                                                <?php else: ?>
                                                    <?= $modelSubject->getSubjectVidName(); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php foreach ($modelSubject->getTeachersLoads() as $item => $modelTeachersLoad): ?>
                                                    <div>
                                                        <?php
                                                        $editable = Editable::begin([
                                                            'model' => $modelTeachersLoad,
                                                            'attribute' => "[{$modelSubject->id}][{$modelTeachersLoad->id}]teachers_id",
                                                            'header' => 'нагрузку',
                                                            'displayValueConfig' => RefBook::find('teachers_load_display')->getList(),
                                                            'format' => Editable::FORMAT_LINK,
                                                            'inputType' => Editable::INPUT_DEPDROP,
                                                            'options' => [
                                                                'id' => $modelSubject->id . "-" . $modelTeachersLoad->id,
                                                                'type' => DepDrop::TYPE_SELECT2,
                                                                'options' => ['placeholder' => Yii::t('art/teachers', 'Select Teacher...')],
                                                                'select2Options' => [
                                                                    'pluginOptions' => [
                                                                        'dropdownParent' => "#" . $modelSubject->id . "-" . $modelTeachersLoad->id . "-popover",
                                                                        'allowClear' => true,
                                                                    ]
                                                                ],
                                                                'data' => Teachers::getTeachersList($modelTeachersLoad->direction_id),
                                                                'pluginOptions' => [
                                                                    'depends' => [$modelSubject->id . "-" . $modelTeachersLoad->id . "-direction_id"],
                                                                    'url' => Url::to(['/teachers/default/teachers'])
                                                                ]
                                                            ],
                                                            'formOptions' => [
                                                                'action' => Url::toRoute([
                                                                    '/teachers/default/set-load',
                                                                    'teachers_load_id' => $modelTeachersLoad->id,
                                                                    'studyplan_subject_id' => $modelSubject->id
                                                                ]),
                                                            ],
                                                            'pluginEvents' => [
                                                                "editableSubmit" => new JsExpression($JSSubmit),
                                                            ]
                                                        ]);
                                                        $form = $editable->getForm();
                                                        $editable->beforeInput = \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}][{$modelTeachersLoad->id}]kv-editable-depdrop", '1')
                                                            .
                                                            $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]direction_id")
                                                                ->dropDownList(['' => Yii::t('art/teachers', 'Select Direction...')] + \common\models\guidejob\Direction::getDirectionList(),
                                                                    ['id' => $modelSubject->id . "-" . $modelTeachersLoad->id . "-direction_id"])->label(false) . "\n";

                                                        $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]week_time")->textInput(['placeholder' => Yii::t('art/guide', 'Enter week time...')])->label(false) . "\n";
                                                        Editable::end();
                                                        ?>
                                                    </div>
                                                <?php endforeach; ?>
                                                <div class="pull-right">
                                                    <?php
                                                    $modelTeachersLoad = new \common\models\teachers\TeachersLoad();
                                                    $editable = Editable::begin([
                                                        'model' => $modelTeachersLoad,
                                                        'attribute' => "[{$modelSubject->id}][0]teachers_id",
                                                        'header' => 'нагрузку',
                                                        'displayValueConfig' => RefBook::find('teachers_load_display')->getList(),
                                                        'valueIfNull' => false,
                                                        'defaultEditableBtnIcon' => '<i class="glyphicon glyphicon-plus"></i>',
                                                        'buttonsTemplate' => "{reset}{submit}",
                                                        'format' => Editable::FORMAT_BUTTON,
                                                        'inputType' => Editable::INPUT_DEPDROP,
                                                        'options' => [
                                                            'type' => DepDrop::TYPE_SELECT2,
                                                            'options' => ['placeholder' => Yii::t('art/teachers', 'Select Teacher...')],
                                                            'select2Options' => [
                                                                'pluginOptions' => [
                                                                    'dropdownParent' => "#" . $modelSubject->id . "-popover",
                                                                    'allowClear' => true,
                                                                ]
                                                            ],
                                                            'data' => Teachers::getTeachersList($modelTeachersLoad->direction_id),
                                                            'pluginOptions' => [
                                                                'depends' => [$modelSubject->id . "-direction_id"],
                                                                'url' => Url::to(['/teachers/default/teachers'])
                                                            ]
                                                        ],
                                                        'formOptions' => [
                                                            'action' => Url::toRoute([
                                                                '/teachers/default/set-load',
                                                                'studyplan_subject_id' => $modelSubject->id
                                                            ]),
                                                        ],
                                                        'pluginEvents' => [
                                                            "editableSubmit" => new JsExpression($JSSubmit),
                                                        ],

                                                    ]);
                                                    $form = $editable->getForm();
                                                    $editable->beforeInput = \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}]kv-editable-depdrop", '1')
                                                        .
                                                        $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]direction_id")
                                                            ->dropDownList(['' => Yii::t('art/teachers', 'Select Direction...')] + \common\models\guidejob\Direction::getDirectionList(),
                                                                ['id' => $modelSubject->id . "-direction_id"])->label(false) . "\n";

                                                    $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]week_time")->textInput(['placeholder' => Yii::t('art/guide', 'Enter week time...')])->label(false) . "\n";
                                                    Editable::end();
                                                    ?>
                                                </div>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
$('.kv-editable-remove').on('click', function (e) {
         e.preventDefault();
        console.log('click');
});
JS;

$this->registerJs($js);
?>


