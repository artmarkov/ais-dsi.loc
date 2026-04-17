<?php

use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = 'Итоги мероприятия';
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['schoolplan/default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$readonlyResult = (\artsoft\Art::isFrontend() && $model->isAuthor() && Yii::$app->formatter->asTimestamp($model->datetime_out) < time()) ? false : $readonly; // для возможности редактировать результаты и добавлять файлы автору мероприятия

?>
    <div class="schoolplan-plan-form">

        <?php
        $form = ActiveForm::begin([
            'id' => 'schoolplan-plan-form',
            'validateOnBlur' => false,
            'options' => ['enctype' => 'multipart/form-data'],
        ])
        ?>
        <div class="panel">
            <div class="panel-body">
                <?php if (!$model->isNewRecord): ?>
                    <?php $text = $readonlyResult ? 'Мероприятие завершено. Вы можете добавить информацию в блоки "Итоги мероприятия"' : 'После окончания мероприятия в любом статусе, Вы сможете добавить информацию в блоки "Итоги мероприятия"' ?>
                    <?= (\artsoft\Art::isFrontend() && $model->isAuthor()) ? \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> ' . $text,
                        'options' => ['class' => 'alert-info'],
                    ]) : null;
                    ?>
                <?php endif; ?>
                <div class="panel">
                    <div class="panel-heading">
                        Итоги мероприятия
                    </div>
                    <div class="panel-body">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'title',
                                'datetime_in',
                                'datetime_out',
                            ],
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Материаллы для отчета
                            </div>
                            <div class="panel-body">
                                <div class="row" id="file">
                                    <div class="col-sm-12">
                                        <?= \yii\bootstrap\Alert::widget([
                                            'body' => '<i class="fa fa-info"></i> Максимальный размер файла: 5 Mb',
                                            'options' => ['class' => 'alert-info'],
                                        ]);
                                        ?>
                                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], /*'pluginOptions' => ['theme' => 'explorer'],*/
                                            'disabled' => $readonlyResult]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Общие итоги мероприятия
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'result')->textarea(['rows' => 6, 'disabled' => $readonlyResult])->hint('Введите данные о результатах мероприятия с указанием фамилии и имени учащихся, ФИО преподавателей и концертмейстеров в формате: Иванов Иван (преп. Петров П.П., конц. Сидоров С.С.) – лауреат I степени. В случае, если учащийся не получил награды по итогам мероприятия, он вносится как участник. Если участие в мероприятии не состоялось, укажите причину, по которой оно было отменено.') ?>

                                        <?= $form->field($model, 'num_users')->textInput(['disabled' => $readonlyResult])->hint('Укажите, какое количество человек предположительно будет принимать участие в мероприятии. В случае, если Вы сами являетесь организатором, указывается точное количество участников, включая организаторов и преподавателей. Если вы не являетесь организатором указанного мероприятия, то в критерии учитываются только участники непосредственно от учреждения.') ?>

                                        <?= $form->field($model, 'num_winners')->textInput(['disabled' => $readonlyResult]) ?>

                                        <?= $form->field($model, 'num_visitors')->textInput(['disabled' => $readonlyResult]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonlyResult ? \artsoft\helpers\ButtonHelper::saveButton() : ''; ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>