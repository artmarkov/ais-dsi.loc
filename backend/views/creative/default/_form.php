<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeWorks;
use common\models\creative\CreativeCategory;
use artsoft\models\User;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="creative-works-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'creative-works-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textarea(['rows' => 3]) ?>
                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                            <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\own\Department::getDepartmentList(),
                                'options' => [
                                    //'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>
                            <?= $form->field($model, 'teachers_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\user\UserCommon::getTeachersList(),
                                'options' => [
                                    //'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/teachers', 'Teachers'));
                            ?>

                            <?= $form->field($model, 'category_id')->dropDownList(CreativeCategory::getCreativeCategoryList(), ['prompt' => '', 'encodeSpaces' => true]) ?>
                            <?= $form->field($model, 'published_at')->widget(DatePicker::class)->textInput(['autocomplete' => 'off']); ?>
                            <?= $form->field($model, 'status')->dropDownList(CreativeWorks::getStatusList()) ?>

                            <?php if (!$model->isNewRecord): ?>
                                <?= $form->field($model, 'created_by')->dropDownList(User::getUsersList()) ?>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php if (!$model->isNewRecord) : ?>
<!--                            --><?//= \backend\widgets\WorksAuthorWidget::widget(['model' => $model]); ?>
                    <?php endif; ?>
                    <!--<? //php echo '<pre>' . print_r($model->imagesLinksData, true) . '</pre>'; ?>-->
<!--                    --><?//= \kartik\file\FileInput::widget([
//                        'name' => 'ImageManager[attachment]',
//                        'options' => [
//                            'multiple' => true
//                        ],
//                        'pluginOptions' => [
//                            'deleteUrl' => Url::toRoute(['/service/image-manager/delete-image']),
//                            'initialPreview' => $model->imagesLinks,
//                            'initialPreviewAsData' => true,
//                            'initialPreviewFileType' => 'image',
//                            'overwriteInitial' => false,
//                            'initialPreviewConfig' => $model->imagesLinksData,
//                            'allowedFileExtensions' => ["jpg", "png", "mp4", "pdf"],
//                            'uploadUrl' => Url::to(['/service/image-manager/file-upload']),
//                            'uploadExtraData' => [
//                                'ImageManager[class]' => $model->formName(),
//                                'ImageManager[item_id]' => $model->id
//                            ],
//                            'maxFileCount' => 10,
//                        ],
//                        'pluginEvents' => [
//                            'filesorted' => new \yii\web\JsExpression('function(event, params){
//                                              $.post("' . Url::toRoute(["/service/image-manager/sort-image", "id" => $model->id]) . '", {sort: params});
//                                        }')
//                        ],
//                    ]);
//                    ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
        <?php ActiveForm::end(); ?>
</div>