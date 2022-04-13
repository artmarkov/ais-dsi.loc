<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\own\Department;
use common\models\schoolplan\Schoolplan;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-plan-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'schoolplan-plan-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка мероприятия
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput()->hint('Выберите запланированную дату и укажите время проведения мероприятия. Если на момент введения Вы не обладаете информацией о точном времени проведения мероприятия, указывается приблизительное время.'); ?>

                    <?= $form->field($model, 'datetime_out')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput() ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Введите официальное название мероприятия, которое указано в положении. Например: «X Международный фестиваль «Ипполитовская хоровая весна». В случае проведения самостоятельного мероприятия вместе с более крупным, укажите название более крупного мероприятия, используя связку «в рамках», например: Мастер-класс по лепке из глины в рамках Большого фестиваля детских школ искусств. Название указывается в кавычках. Если мероприятие посвящено какому-либо событию и (или) памятной дате, вводится пояснение с указанием основной цели мероприятия. Например: Концерт «Симфония весны», посвящённый Международному женскому дню 8 Марта.') ?>

                    <?= $form->field($model, 'places')->textInput(['maxlength' => true])->hint('Укажите место проведения в соответствии с фактическим местом, где проводится мероприятие (в случае, если мероприятие будет проводиться на разных площадках, указывается основное место его проведения. Данные вводятся в формате полного названия места. Например: Парк культуры и отдыха имени Горького). Если мероприятие проводится дистанционно, то местом проведения указывается «сеть интернет».') ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>

                    <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => Department::getDepartmentList(),
                        'options' => [
                           // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                    <?= $form->field($model->loadDefaultValues(), 'executors_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                           // 'disabled' => true,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                           // 'minimumInputLength' => 3,
                        ],

                    ])->label(Yii::t('art', 'Executors'));

                    ?>

                    <?= $form->field($model, 'category_id')->textInput() ?>

                    <?= $form->field($model, 'form_partic')->radioList(Schoolplan::getFormParticList()) ?>

                    <?= $form->field($model, 'partic_price')->textInput(['maxlength' => true])->hint('Укажите стоимость участия одного человека/организации в рублях.') ?>

                    <?= $form->field($model, 'visit_poss')->radioList(Schoolplan::getVisitPossList()) ?>

                    <?= $form->field($model, 'visit_content')->textarea(['rows' => 2])->hint('Укажите, является запланированное мероприятие открытым или закрытым. Открытое мероприятие - вход возможен для всех желающих (в независимости от того, платный он или нет). Закрытое мероприятие - вход возможен для ограниченного круга лиц, например: «Приглашаются выпускники и их родители».') ?>

                    <?= $form->field($model, 'important_event')->textInput() ?>

                    <?= $form->field($model, 'region_partners')->textarea(['rows' => 2]) ?>

                    <?= $form->field($model, 'site_url')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'site_media')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('Введите полное описание мероприятия, включающее важную и существенную информацию. Оно может содержать программу мероприятия, историю возникновения, значимость мероприятия для учреждения и участников, поименное перечисление участников, выступающих, организаторов, направленность мероприятия в форме развернутого ответа. Объем текста - не менее 1000 знаков и не более 4000 знаков.') ?>

                    <?= $form->field($model, 'rider')->textarea(['rows' => 3])->hint('свет, микрофоны, хоровые станки и т.п.') ?>

                    <?= $form->field($model, 'result')->textarea(['rows' => 6])->hint('Введите данные о результатах мероприятия с указанием фамилии и имени учащихся, ФИО преподавателей и концертмейстеров в формате: Иванов Иван (преп. Петров П.П., конц. Сидоров С.С.) – лауреат I степени. В случае, если учащийся не получил награды по итогам мероприятия, он вносится как участник. Если участие в мероприятии не состоялось, укажите причину, по которой оно было отменено.') ?>

                    <?= $form->field($model, 'num_users')->textInput()->hint('Укажите, какое количество человек предположительно будет принимать участие в мероприятии. В случае, если Вы сами являетесь организатором, указывается точное количество участников, включая организаторов и преподавателей. Если вы не являетесь организатором указанного мероприятия, то в критерии учитываются только участники непосредственно от учреждения.') ?>

                    <?= $form->field($model, 'num_winners')->textInput() ?>

                    <?= $form->field($model, 'num_visitors')->textInput() ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?=  \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
