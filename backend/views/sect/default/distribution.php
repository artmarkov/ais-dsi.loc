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
/* @var $model_date */
/* @var $modelsSubjectSectStudyplan \common\models\subjectsect\SubjectSectStudyplan */

$this->title = Yii::t('art/guide', 'Teachers Load');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="subject-sect-form">

    <div class="panel">
        <div class="panel-heading">
            Распределение по группам <?= RefBook::find('subject_name')->getValue($model->subject_id); ?>
            <?= $this->render('_search', compact('model_date')) ?>
        </div>
        <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
                'inputOptions' => ['readonly' => $readonly]
            ],
            'id' => 'subject-sect-form',
            'validateOnBlur' => false,
        ]);

        $sub_group_qty = $model->sub_group_qty;
        $term_mastering = $model->union->term_mastering;
        $group = 0;
        ?>
        <div class="panel-body">
            <div class="row">
                <div class="table-responsive kv-grid-container">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-warning">
                        <tr>
                            <th rowspan="2" class="text-center" style="vertical-align: middle;min-width: 100px;">
                                Группа
                            </th>
                            <th colspan="<?= $term_mastering ?>" class="text-center" style="min-width: 100px">
                                Годы обучения
                            </th>
                        </tr>
                        <tr>
                            <?php for ($i = 1; $i <= $term_mastering; $i++): ?>
                                <th class="text-center" style="min-width: 300px;"><?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan): ?>
                            <?php
                            if ($index == $term_mastering * $group) {
                                $group++;
                                echo '<tr>
                                <td class="text-center" style="vertical-align: middle;">' . sprintf('%02d', $group) . '</td>';
                            }
                            echo '<td>';
                            echo RefBook::find('sect_memo_1')->getValue($modelSubjectSectStudyplan->id);

                            // necessary for update action.
                            if (!$modelSubjectSectStudyplan->isNewRecord) {
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]id");
                            }
                            echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_sect_id");
                            echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]group_num");
                            echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]plan_year");
                            echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]course");
                            echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_type_id");
                            echo SortableInput::widget([
                                'model' => $modelSubjectSectStudyplan,
                                'attribute' => "[{$index}]studyplan_subject_list",
                                'hideInput' => true,
                                'sortableOptions' => [
                                    'itemOptions' => ['class' => 'alert alert-success'],
                                    'options' => ['style' => 'min-height: 40px'],
                                    'connected' => true,
                                ],
                                'options' => ['class' => 'form-control', 'readonly' => true],
                                'delimiter' => ',',
                                'items' => $modelSubjectSectStudyplan->getSubjectSectStudyplans($readonly),
                            ]);

                            echo '<p class="help-block help-block-error"></p>
                                    </td>';
                            if ($index == $term_mastering * $group) {
                                echo '</tr>';
                            }
                            ?>
                        <?php endforeach; ?>
                        <tr>
                            <td>

                            </td>
                            <?php for ($i = 1; $i <= $term_mastering; $i++): ?>
                                <td>
                                    <?= SortableInput::widget([
                                        'name' => "[{$i}]studyplan",
                                        'items' => $model->getStudyplanForUnion($model_date->plan_year, $i, $readonly),
                                        'hideInput' => true,
                                        'sortableOptions' => [
                                            'itemOptions' => ['class' => 'alert alert-info'],
                                            'options' => ['style' => 'min-height: 40px'],
                                            'connected' => true,
                                        ],
                                        'options' => ['class' => 'form-control', 'readonly' => true]
                                    ]);
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        </tbody>
                        <?php
                        //                                            echo '<pre>' . print_r($model_date, true) . '</pre>';

                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::saveButton() : \artsoft\helpers\ButtonHelper::updateButton($model); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


