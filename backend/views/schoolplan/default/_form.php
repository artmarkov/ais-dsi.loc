<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\own\Department;
use common\models\schoolplan\Schoolplan;
use artsoft\helpers\Html;
use common\models\user\UserCommon;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $modelActivitiesOver common\models\activities\ActivitiesOver */
/* @var $form artsoft\widgets\ActiveForm */

//echo '<pre>' . print_r($model, true) . '</pre>';
?>
<?php
$subject_category_name_list = RefBook::find('subject_category_name', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList();
$subject_vid_name_list = RefBook::find('subject_vid_name', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList();
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
        <div class="panel-heading">
            Карточка мероприятия
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <?php if (!$model->isNewRecord): ?>
                <?php $text = $readonlyResult ? 'Мероприятие завершено. Вы можете добавить информация в блоки "Загруженные материалы" и "Итоги мероприятия"' : 'После окончания мероприятия в любом статусе, Вы сможете добавить информацию в блоки "Загруженные материалы" и "Итоги мероприятия"' ?>
    <?= (\artsoft\Art::isFrontend() && $model->isAuthor()) ? \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info"></i> ' . $text,
                'options' => ['class' => 'alert-info'],
            ]) : null;
            ?>
            <?php endif; ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Дата и время мероприятия
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= $form->field($model, 'date_in')->widget(\kartik\date\DatePicker::className(), ['pluginOptions' => [
                                        'orientation' => 'bottom',
                                    ]])->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly])->hint('Выберите запланированную дату.'); ?>

                                    <?= $form->field($model, 'time_in')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly])->hint('Укажите время проведения мероприятия. Если на момент введения Вы не обладаете информацией о точном времени проведения мероприятия, указывается приблизительное время.'); ?>

                                    <?= $form->field($model, 'date_out')->widget(\kartik\date\DatePicker::className(), ['pluginOptions' => [
                                        'orientation' => 'bottom',
                                    ]])->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                                    <?= $form->field($model, 'time_out')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Основные сведения
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php
                                        //                        echo $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly])->hint('Выберите запланированную дату и укажите время проведения мероприятия. Если на момент введения Вы не обладаете информацией о точном времени проведения мероприятия, указывается приблизительное время.');
                                        ?>
                                        <?php
                                        //                        echo $form->field($model, 'datetime_out')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly])
                                        ?>

                                        <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'disabled' => $readonly])->hint('Введите официальное название мероприятия, которое указано в положении. Например: «X Международный фестиваль «Ипполитовская хоровая весна». В случае проведения самостоятельного мероприятия вместе с более крупным, укажите название более крупного мероприятия, используя связку «в рамках», например: Мастер-класс по лепке из глины в рамках Большого фестиваля детских школ искусств. Название указывается в кавычках. Если мероприятие посвящено какому-либо событию и (или) памятной дате, вводится пояснение с указанием основной цели мероприятия. Например: Концерт «Симфония весны», посвящённый Международному женскому дню 8 Марта.') ?>

                                        <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                                            'data' => Department::getDepartmentList(),
                                            'options' => [
                                                'disabled' => $readonly,
                                                'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                                                'multiple' => true,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label(Yii::t('art/guide', 'Department'));
                                        ?>

                                        <?= $form->field($model->loadDefaultValues(), 'executors_list')->widget(\kartik\select2\Select2::class, [
                                            'data' => RefBook::find('teachers_fio', UserCommon::STATUS_ACTIVE)->getList(),
                                            'showToggleAll' => false,
                                            'options' => [
                                                'disabled' => $readonly,
                                                'placeholder' => Yii::t('art', 'Select...'),
                                                'multiple' => true,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                //'minimumInputLength' => 3,
                                            ],

                                        ]);
                                        ?>


                                        <?= $form->field($model, 'category_id')->widget(\kartik\tree\TreeViewInput::class, [
                                            'id' => "schoolplan_category_tree",
                                            'options' => [
                                                'disabled' => $readonly,
                                            ],
                                            'query' => \common\models\guidesys\GuidePlanTree::find()->addOrderBy('root, lft'),
                                            'dropdownConfig' => [
                                                'input' => ['placeholder' => 'Выберите категорию мероприятия...'],
                                            ],
                                            'fontAwesome' => true,
                                            'multiple' => false,
                                            'rootOptions' => [
                                                'label' => '',
                                                'class' => 'text-default'
                                            ],
                                            'childNodeIconOptions' => ['class' => ''],
                                            'defaultParentNodeIcon' => '',
                                            'defaultParentNodeOpenIcon' => '',
                                            'defaultChildNodeIcon' => '',
                                            'childNodeIconOptions' => ['class' => ''],
                                            'parentNodeIconOptions' => ['class' => ''],
                                        ]);
                                        ?>
                                        <div class="spinner">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9">
                                                <?= \kartik\spinner\Spinner::widget(['preset' => 'small', 'align' => 'left']); ?>
                                            </div>
                                        </div>
                                        <?= $form->field($model->loadDefaultValues(), 'formPlaces')->radioList(Schoolplan::getFormPlacesList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                                        <?= $form->field($model, 'places')->textInput(['maxlength' => true])->hint('Укажите место проведения в соответствии с фактическим местом, где проводится мероприятие (в случае, если мероприятие будет проводиться на разных площадках, указывается основное место его проведения. Данные вводятся в формате полного названия места. Например: Парк культуры и отдыха имени Горького). Если мероприятие проводится дистанционно, то местом проведения указывается «сеть интернет».') ?>

                                        <?= $form->field($model, 'auditory_id')->widget(\kartik\select2\Select2::class, [
                                            'data' => RefBook::find('auditory_memo_1')->getList(),
                                            'showToggleAll' => false,
                                            'options' => [
                                                'disabled' => $readonly,
                                                'placeholder' => Yii::t('art', 'Select...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                //'minimumInputLength' => 3,
                                            ],

                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!$model->isNewRecord) : ?>
                            <?php if ($model->category->preparing_flag) : ?>

                                <?= $form->field($model, 'period_over_flag')->checkbox(['disabled' => $readonly]) ?>

                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="activitiesOver" class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Подготовка к мероприятию
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <?= $form->field($model, 'period_over')->dropDownList(Schoolplan::getPeriodOverList(), ['disabled' => $readonly]) ?>

                                        <?= $form->field($model, 'executor_over_id')->widget(\kartik\select2\Select2::class, [
                                            'data' => RefBook::find('teachers_fio', UserCommon::STATUS_ACTIVE)->getList(),
                                            'showToggleAll' => false,
                                            'options' => [
                                                'disabled' => $readonly,
                                                'placeholder' => Yii::t('art', 'Select...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                // 'minimumInputLength' => 3,
                                            ],

                                        ]); ?>

                                        <?= $form->field($model->loadDefaultValues(), 'title_over')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!$model->isNewRecord) : ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Дополнительные сведения
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <?= $form->field($model, 'form_partic')->radioList(Schoolplan::getFormParticList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                                            <?= $form->field($model, 'partic_price')->widget(\kartik\money\MaskMoney::class, [
                                                'pluginOptions' => [
                                                    'prefix' => '₽ ',
                                                    'suffix' => ' ',
                                                    'allowNegative' => false,
                                                    'disabled' => $readonly
                                                ]
                                            ])->hint('Укажите стоимость участия одного человека/организации в рублях.') ?>

                                            <?= $form->field($model, 'visit_poss')->radioList(Schoolplan::getVisitPossList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                                            <?= $form->field($model, 'visit_content')->textarea(['rows' => 2, 'disabled' => $readonly])->hint('Укажите, является запланированное мероприятие открытым или закрытым. Открытое мероприятие - вход возможен для всех желающих (в независимости от того, платный он или нет). Закрытое мероприятие - вход возможен для ограниченного круга лиц, например: «Приглашаются выпускники и их родители».') ?>

                                            <?= $form->field($model, 'format_event')->radioList(Schoolplan::getFormatList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                                            <?= $form->field($model, 'important_event')->radioList(Schoolplan::getImportantList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                                            <?php if ($model->category->partners_flag) : ?>

                                                <?= $form->field($model, 'region_partners')->textInput(['maxlength' => true]) ?>

                                            <?php endif; ?>

                                            <?= $form->field($model, 'site_url')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                                            <?= $form->field($model, 'site_media')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                                            <div id="count_schoolplan-description" class="fa-pull-right"></div>
                                            <?= $form->field($model, 'description')->textarea(['rows' => 6, 'disabled' => $readonly])->hint('Введите полное описание мероприятия, включающее важную и существенную информацию. Оно может содержать программу мероприятия, историю возникновения, значимость мероприятия для учреждения и участников, поименное перечисление участников, выступающих, организаторов, направленность мероприятия в форме развернутого ответа.') ?>

                                            <?php if ($model->category->rider_flag) : ?>

                                                <?= $form->field($model, 'rider')->textarea(['rows' => 3, 'disabled' => $readonly])->hint('свет, микрофоны, хоровые станки и т.п.') ?>

                                            <?php endif; ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($model->category->afisha_flag) : ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        Загруженные материалы
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?= \yii\bootstrap\Alert::widget([
                                                    'body' => '<i class="fa fa-info"></i> Максимальный размер файла: 3 Mb',
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
                    <?php endif; ?>
                    <?php if ($model->category->commission_sell == 1) : ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        Протокол мероприятия
                                    </div>
                                    <div class="panel-body">
                                        <?= $form->field($model, 'protocolFlag')->checkbox(['disabled' => $readonly])->label('Раскрыть карточку комиссии для протокола мероприятия') ?>
                                        <div class="col-sm-12" id="protocol">
                                        <?php echo \yii\bootstrap\Alert::widget([
                                            'body' => '<i class="fa fa-info-circle"></i> Оценки в протоколе могут выставлять только Председатель комиссии, Заместитель председателя комиссии и Секретарь комиссии',
                                            'options' => ['class' => 'alert-info'],
                                        ]);
                                        ?>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <?= $form->field($model, 'protocolLeaderFlag')->checkbox(['disabled' => $readonly])->label('Заполнить поле Председатель комиссии от руки') ?>
                                                    <div id="protocolLeaderId">
                                                        <?= $form->field($model, 'protocol_leader_id')->widget(\kartik\select2\Select2::class, [
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
                                                    </div>
                                                    <div id="protocolLeaderName">
                                                        <?= $form->field($model, 'protocol_leader_name')->textInput(['disabled' => $readonly])->hint('Введите Председателя комиссии(Фамилия Имя Отчество)') ?>
                                                    </div>
                                                    <?= $form->field($model, 'protocol_soleader_id')->widget(\kartik\select2\Select2::class, [
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
                                                    <?= $form->field($model, 'protocol_secretary_id')->widget(\kartik\select2\Select2::class, [
                                                        'data' => User::getUsersByIds(User::getUsersByRole('department,administrator,employees')),
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
                                                    <?= $form->field($model, 'protocol_members_list')->widget(\kartik\select2\Select2::class, [
    //                                                    'data' => \common\models\teachers\Teachers::getUserTeachersForDepartment($model->department_list),
                                                        'data' => User::getUsersByIds(User::getUsersByRole('department,administrator')),
                                                        'showToggleAll' => false,
                                                        'options' => [
                                                            'disabled' => $readonly,
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                            'multiple' => true,
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true,
                                                        ],

                                                    ]);

                                                    ?>

                                                    <?= $form->field($model, 'protocol_subject_cat_id')->widget(\kartik\select2\Select2::class, [
                                                        'data' => $subject_category_name_list,
                                                        'options' => [
                                                            'id' => 'protocol_subject_cat_id',
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                        ],
                                                        'pluginOptions' => [
                                                            'disabled' => $readonly,
                                                            'allowClear' => true
                                                        ],

                                                    ]); ?>
                                                    <?= $form->field($model, 'protocol_subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                                                        'data' => \common\models\subject\Subject::getSubjectByCategory($model->protocol_subject_cat_id),
                                                        'options' => [
                                                            'id' => 'protocol_subject_id',
                                                            'disabled' => $readonly,
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                            'multiple' => true,
                                                        ],
                                                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                                        'pluginOptions' => [
                                                            'depends' => ['protocol_subject_cat_id'],
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                            'url' => Url::to(['/subject/default/subject'])
                                                        ],

                                                    ]);
                                                    ?>
                                                    <?= $form->field($model, 'protocol_subject_vid_id')->widget(\kartik\select2\Select2::class, [
                                                        'data' => $subject_vid_name_list,
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

                                                    <?= $form->field($model, 'protocol_class_list')->widget(\kartik\select2\Select2::class, [
                                                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                                                        'showToggleAll' => false,
                                                        'options' => [
                                                            'disabled' => $readonly,
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                            'multiple' => true,
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true,
                                                        ],

                                                    ]);

                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Итоги мероприятия
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
                <?php endif; ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Статус мероприятия
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?= $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [
                                            'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees']),
                                            'showToggleAll' => false,
                                            'options' => [
                                                'disabled' => \artsoft\Art::isFrontend() ? true : $readonly,
                                                'placeholder' => Yii::t('art', 'Select...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                //'minimumInputLength' => 3,
                                            ],

                                        ]);
                                        ?>
                                        <?= $form->field($model, 'signer_id')->widget(\kartik\select2\Select2::class, [
                                            'data' => User::getUsersByIds(User::getUsersByRole('signerSchoolplan')),
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
                                        <?= $form->field($model->loadDefaultValues(), 'doc_status')->widget(\kartik\select2\Select2::class, [
                                            'data' => Schoolplan::getDocStatusList(),
                                            'showToggleAll' => false,
                                            'options' => [
                                                'disabled' => true,
                                                'placeholder' => Yii::t('art', 'Select...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                //'minimumInputLength' => 3,
                                            ],

                                        ]);
                                        ?>

                                        <?php if (!$model->isNewRecord && $model->category->bars_flag && \artsoft\Art::isBackend()) : ?>
                                            <?= $form->field($model, 'bars_flag')->checkbox(['disabled' => $readonly]) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!$model->isNewRecord && \artsoft\Art::isBackend() && !$readonly) : ?>
                                    <div class="row">
                                        <hr>
                                        <div class="col-sm-12">
                                            <?= $form->field($model, 'admin_flag')->checkbox(['disabled' => $readonly])->label('Добавить сообщение') ?>
                                            <div id="send_admin_message">
                                                <?= $form->field($model, 'admin_message')->textInput()->hint('Введите сообщение для автора мароприятия и нажмите "Отправить на доработку"') ?>
                                            </div>
                                        </div>
                                    </div>Статус подписи
                                    <div class="form-group btn-group">
                                        <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Согласовать', ['class' => 'btn btn-sm btn-success', 'name' => 'submitAction', 'value' => 'approve', 'disabled' => $model->doc_status == 1]); ?>
                                        <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить на доработку', ['class' => 'btn btn-sm btn-default pull-right', 'name' => 'submitAction', 'value' => 'modif']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!$model->isNewRecord && \artsoft\Art::isFrontend() && $model->isAuthor()): ?>
                                    <div class="form-group btn-group">
                                        <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Отправить на согласование', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'send_approve', 'disabled' => in_array($model->doc_status, [1, 2]) ? true : false]); ?>
                                        <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Внести изменения', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'make_changes', 'disabled' => in_array($model->doc_status, [0, 3]) ? true : false]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : ($model->isAuthor() && $model->doc_status == Schoolplan::DOC_STATUS_DRAFT ? \artsoft\helpers\ButtonHelper::viewButtons($model) : \artsoft\helpers\ButtonHelper::exitButton()); ?>
                    <?= $readonly && !$readonlyResult ? \artsoft\helpers\ButtonHelper::saveButton() : ''; ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$js = <<<JS
$("#schoolplan-date_in").on("input change", function () {
     var value = $(this).val();
     // if(!$("#schoolplan-date_out").val()) {
        $("#schoolplan-date_out").val(value);
     // }
   });
// Показ поля Стоимость участия
let input =  $('input[type=radio][name="Schoolplan[form_partic]"]');
let field =  $('.field-schoolplan-partic_price');
    field.show();
    input.each(function () {
        if($(this).val() === '1' && $(this).prop('checked')) {
            field.hide();
        } 
    });
    input.click(function() {
       $(this).val() === '2' ? field.show() : field.hide();
     });
    // Показ модуля Подготовка к мероприятию
    $('input[type=checkbox][name="Schoolplan[period_over_flag]"]').prop('checked') ? $('#activitiesOver').show() : $('#activitiesOver').hide();
    $('input[type=checkbox][name="Schoolplan[period_over_flag]"]').click(function() {
       $(this).prop('checked') ? $('#activitiesOver').show() : $('#activitiesOver').hide();
     });
     // Показ модуля админа
    $('input[type=checkbox][name="Schoolplan[admin_flag]"]').prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
    $('input[type=checkbox][name="Schoolplan[admin_flag]"]').click(function() {
       $(this).prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
     });
    // Подсчет символов
if(document.getElementById('schoolplan-description')) {
    document.getElementById('schoolplan-description').addEventListener("input", function () {
        document.getElementById('count_schoolplan-description').innerText = 'Введено: ' + this.value.length + ' символа(ов), включая пробелы.';
        console.log(this.value.length);
        });
    }
 // Введено вручную
     if($('input[type=checkbox][name="Schoolplan[protocolLeaderFlag]"]').prop('checked')) {
       $('#protocolLeaderName').show();
       $('#protocolLeaderId').hide();
       } else {
       $('#protocolLeaderId').show();
       $('#protocolLeaderName').hide();
       }
    $('input[type=checkbox][name="Schoolplan[protocolLeaderFlag]"]').click(function() {
       if($(this).prop('checked')) {
       $('#protocolLeaderName').show();
       $('#protocolLeaderId').hide();
       } else {
       $('#protocolLeaderId').show();
       $('#protocolLeaderName').hide();
       }
           
     });
     // Введено вручную
     if($('input[type=checkbox][name="Schoolplan[protocolFlag]"]').prop('checked')) {
       $('#protocol').show();
       } else {
       $('#protocol').hide();
       }
    $('input[type=checkbox][name="Schoolplan[protocolFlag]"]').click(function() {
       if($(this).prop('checked')) {
       $('#protocol').show();
       } else {
       $('#protocol').hide();
       }
           
     });
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

$script = <<< JS
// console.log($('input[type=text][name="Schoolplan[places]"]').val());
// console.log($('select[name="Schoolplan[auditory_id]"]').val());

    // Показ Места проведения или Аудитории
$('.spinner').hide();
  schoolplan($('#schoolplan_category_tree').val()); 

  $("#schoolplan_category_tree").on('treeview:change', function(event, key) {
     // console.log(key);
  schoolplan(key);
  });
  
    
function schoolplan(key) {
  let input =  $('input[type=radio][name="Schoolplan[formPlaces]"]');
  let field1 = $('.field-schoolplan-places');
  let field2 = $('.field-schoolplan-auditory_id');
  let field3 = $('.field-schoolplan-formplaces');
     
  if(key == '') {
        field1.hide(); field2.hide(); field3.hide();
    } else {
   $('.spinner').show();
     $.ajax({
            url: '/admin/schoolplan/default/select',
            type: 'POST',
            data: {
                id: key
            },
            success: function (category) {
               $('.spinner').hide();
                 // console.log(category);
             if(category == 1) {
                 field1.hide(); field2.show(); field3.hide();
             }
             else if(category == 2) {
                 field2.hide(); field1.show(); field3.hide();
             } else {
                 field2.hide(); field1.hide(); field3.show();
                 
                 input.each(function () {
                    if($(this).val() === '1' && $(this).prop('checked')) {
                        field2.show(); field1.hide();
                    } else if($(this).val() === '2' && $(this).prop('checked')) {
                        field1.show(); field2.hide();
                    }
                 });
                 input.click(function() {
                     if($(this).val() === '1') {
                             field1.hide(); field2.show(); 
                         } else if($(this).val() === '2') {
                             field1.show(); field2.hide(); 
                         }
                 });
             }
            },
            error: function () {
                alert('Выберите категорию мероприятия.');
            }
        });
     }
}
JS;
$this->registerJs($script, \yii\web\View::POS_READY);

// Стиль строки подсчета символов
$css = <<<CSS
#count_schoolplan-description {
    font-size: smaller;
    padding-right: 10px;
    color: #a94442;
}
.kv-spin-left {
    float: left;
    margin: 0px 0px 5px 5px;
}

CSS;
$this->registerCss($css);
?>