<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\subjectsect\SubjectSectStudyplan;
use kartik\sortinput\SortableInput;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \common\models\subjectsect\SubjectSect */
/* @var $modelsSubjectSectStudyplan \common\models\subjectsect\SubjectSectStudyplan */

$this->title = Yii::t('art/guide', 'Teachers Load');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="subject-sect-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'subject-sect-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            Распределение по группам <?= RefBook::find('subject_name')->getValue($model->subject_id) ;?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="table-responsive kv-grid-container">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-warning">
                        <tr>
                            <th rowspan="2" class="text-center" style="vertical-align: middle;min-width: 100px;">
                                Группа
                            </th>
                            <th colspan="<?= count($model->course_list) ?>" class="text-center" style="min-width: 100px">
                                Годы обучения
                            </th>
                        </tr>
                        <tr>
                            <?php foreach ($model->course_list as $index => $course): ?>
                                <th class="text-center" style="min-width: 300px;"><?= $course ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php for ($i = 1; $i <= $model->sub_group_qty; $i++): ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;"><?= sprintf('%02d', $i) ?></td>
                                <?php foreach ($modelsSubjectSectStudyplan[$i] as $index => $modelSubjectSectStudyplan): ?>
                                    <td>
                                        <?php echo RefBook::find('sect_memo_1')->getValue($modelSubjectSectStudyplan->id); ?>
                                        <?php
                                        echo SortableInput::widget([
                                            'model' => $modelSubjectSectStudyplan,
                                            'attribute' => "[{$i}][{$index}]studyplan_subject_list",
                                            'hideInput' => true,
                                            'sortableOptions' => [
                                                'itemOptions' => ['class' => 'alert alert-success'],
                                                'options' => ['style' => 'min-height: 40px'],
                                                'connected' => true,
                                            ],
                                            'options' => ['class' => 'form-control', 'readonly' => true],
                                            'delimiter' => ',',
                                            'items' => $modelSubjectSectStudyplan->getSubjectSectStudyplans(),
                                        ]);
                                        ?>
                                        <p class="help-block help-block-error"></p>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                        <?php
                        //                    echo '<pre>' . print_r($modelsSubjectSectStudyplan, true) . '</pre>';

                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


