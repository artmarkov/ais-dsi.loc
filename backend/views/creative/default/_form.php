<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeWorks;
use common\models\creative\CreativeCategory;
use common\models\user\User;
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

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'name')->textarea(['rows' => 3]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo $form->field($model, 'department_list')->widget(\nex\chosen\Chosen::className(), [
                        'items' => CreativeWorks::getDepartmentList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                </div>
               
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php if (!$model->isNewRecord) : ?>
                        <?= \backend\widgets\WorksAuthorWidget::widget(['model' => $model]); ?>
                    <?php endif; ?>
                </div>

            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                           <!--<?//php echo '<pre>' . print_r($model->imagesLinksData, true) . '</pre>'; ?>-->
                            <?= \kartik\file\FileInput::widget([
                                    'name' => 'ImageManager[attachment]',
                                    'options'=>[
                                        'multiple'=>true
                                    ],
                                    'pluginOptions' => [
                                        'deleteUrl' => Url::toRoute(['/service/image-manager/delete-image']),
                                        'initialPreview'=> $model->imagesLinks,
                                        'initialPreviewAsData'=>true,
                                        'initialPreviewFileType' => 'image', 
                                        'overwriteInitial'=>false,
                                        'initialPreviewConfig'=>$model->imagesLinksData,
                                        'allowedFileExtensions' => ["jpg", "png", "mp4", "pdf"],
                                        'uploadUrl' => Url::to(['/service/image-manager/file-upload']),
                                        'uploadExtraData' => [
                                            'ImageManager[class]' => $model->formName(),
                                            'ImageManager[item_id]' => $model->id
                                        ],
                                        'maxFileCount' => 10,
                                    ],
                                    'pluginEvents' => [
                                        'filesorted' => new \yii\web\JsExpression('function(event, params){
                                              $.post("'.Url::toRoute(["/service/image-manager/sort-image", "id" => $model->id]).'", {sort: params});
                                        }')
                                    ],
                              ]);                    
                          ?>
                           
                        </div>

                    </div>
                </div> 
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <?php if (!$model->isNewRecord): ?>

                            <div class="form-group clearfix">
                                <label class="control-label" style="float: left; padding-right: 5px;">
                                    <?= $model->attributeLabels()['created_at'] ?> :
                                </label>
                                <span><?= $model->createdDatetime ?></span>
                            </div>

                            <div class="form-group clearfix">
                                <label class="control-label" style="float: left; padding-right: 5px;">
                                    <?= $model->attributeLabels()['updated_at'] ?> :
                                </label>
                                <span><?= $model->updatedDatetime ?></span>
                            </div>

                            <div class="form-group clearfix">
                                <label class="control-label" style="float: left; padding-right: 5px;">
                                    <?= $model->attributeLabels()['updated_by'] ?> :
                                </label>
                                <span><?= $model->updatedBy->username ?></span>
                            </div>

                        <?php endif; ?>

                        <div class="form-group">
                            <?php  if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/creative/default/index'], ['class' => 'btn btn-default']) ?>
                            <?php  else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/creative/default/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-default',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="record-info">
                        <?= $form->field($model, 'category_id')->dropDownList(CreativeCategory::getCreativeCategoryList(), ['prompt' => '', 'encodeSpaces' => true]) ?>
                        
                        <?php  if($model->published_at) $model->published_at = date("d-m-Y", (integer) mktime(0,0,0, date("m", $model->published_at), date("d", $model->published_at), date("Y", $model->published_at)));  ?>

                        <?= $form->field($model, 'published_at')->widget(DatePicker::classname())->textInput(['autocomplete' => 'off']);
                        ?>
                        <?= $form->field($model, 'status')->dropDownList(CreativeWorks::getStatusList()) ?>

                        <?php if (!$model->isNewRecord): ?>
                            <?= $form->field($model, 'created_by')->dropDownList(User::getUsersList()) ?>
                        <?php endif; ?>

                        <?= $form->field($model, 'comment_status')->dropDownList(CreativeWorks::getCommentStatusList()) ?>                       

                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
