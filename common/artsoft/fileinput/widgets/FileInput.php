<?php

namespace artsoft\fileinput\widgets;

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Description of FileInput
 *
 * @author artmarkov@mail.ru
 */
class FileInput extends \yii\base\Widget
{

    public $model;
    public $id;
    public $options = [];

    public $disabled = false;
    public $pluginOptions = [];
    public $pluginEvents = [];
    public $maxFileCount = 50;
    public $allowedFileExtensions = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'jpg', 'png', 'mp4', 'mp3', 'jpeg'];
    public $maxFileSize = 1024;

    private $rawWidgetHtml;

    public function run()
    {
        if (!isset($this->model)) {
            throw new \yii\base\InvalidConfigException('Model was not found.');
        }

        if (!isset($this->id)) {
            $this->id = $this->model->id;
        }

        $this->buildWidget();

        return $this->getWidgetHtml();
    }

    private function buildWidget()
    {
        $this->rawWidgetHtml = \kartik\file\FileInput::widget([
            'id' => $this->id,
            'name' => 'attachment[]',
            'language' => Yii::$app->language ? Yii::$app->language : 'en',
            'disabled' => $this->disabled,
            'options' => $this->options,
            'pluginOptions' => ArrayHelper::merge([
                'theme' => '', //explorer
                'showCaption' => true,
                'showBrowse' => true,
                'showPreview' => true,
                'showUpload' => true,
                'showRemove' => false,
                'uploadAsync' => false,
                'dropZoneEnabled' => false,
                'showCancel' => true,
                'browseOnZoneClick' => false,
                'maxFileCount' => $this->maxFileCount,
                'validateInitialCount' => true,
                'deleteUrl' => Url::toRoute(['/fileinput/file-manager/delete-file'],'https'),
                'initialPreview' => $this->model->filesLinks,
                'initialPreviewAsData' => true,
                'initialPreviewFileType' => 'image',
                'initialPreviewShowDelete' => true,
                'overwriteInitial' => false,
                'initialPreviewConfig' => $this->model->filesLinksData,
                'maxFileSize' => $this->maxFileSize,
                'allowedFileExtensions' => $this->allowedFileExtensions,
                'uploadUrl' => Url::toRoute(['/fileinput/file-manager/file-upload'],'https'),
                'hideThumbnailContent' => false,
                'preferIconicPreview' => false,
                'previewFileIcon' => '<i class="fa fa-file-o"></i>',
                'previewFileIconSettings' => [
                    'txt' => '<i class="fa fa-file-text-o text-default"></i>',
                    'doc' => '<i class="fa fa-file-word-o text-primary"></i>',
                    'docx' => '<i class="fa fa-file-word-o text-primary"></i>',
                    'xls' => '<i class="fa fa-file-excel-o text-success"></i>',
                    'xlsx' => '<i class="fa fa-file-excel-o text-success"></i>',
                    'ppt' => '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                    'pptx' => '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                    'zip' => '<i class="fa fa-file-archive-o text-muted"></i>',
                    'rar' => '<i class="fa fa-file-archive-o text-muted"></i>',
                    'pdf' => '<i class="fa fa-file-pdf-o text-warning"></i>',
                    'jpg' => '<i class="fa fa-file-image-o text-primary"></i>',
                    'jpeg' => '<i class="fa fa-file-image-o text-primary"></i>',
                    'png' => '<i class="fa fa-file-image-o text-primary"></i>',
                    'mp4' => '<i class="fa fa-film text-primary"></i>',
                    'mp3' => '<i class="fa fa-file-audio-o text-primary"></i>',
                ],
                'fileActionSettings' => [
                    'showDrag' => true,
                    'showZoom' => true,
                    'showRemove' => true,
                    'removeIcon' => '<i class="fa fa-trash"></i>',
                    'zoomIcon' => '<i class="fa fa-search"></i>',
                    'downloadIcon' => '<i class="fa fa-download"></i>',
                    'dragIcon' => '<i class="fa fa-arrows"></i>',
                ],
                'previewZoomButtonIcons' => [
                    'prev' => '<i class="fa fa-backward"></i>',
                    'next' => '<i class="fa fa-forward"></i>',
                    'toggleheader' => '<i class="fa fa-arrows-v"></i>',
                    'fullscreen' => '<i class="fa fa-arrows-alt"></i>',
                    'borderless' => '<i class="fa fa-expand"></i>',
                    'close' => '<i class="fa fa-times"></i>'
                ],
                'uploadExtraData' => [
                    'FileManager[class]' => $this->model->formName(),
                    'FileManager[item_id]' => $this->id
                ],
            ], $this->pluginOptions),
            'pluginEvents' => ArrayHelper::merge([
                'filesorted' => new \yii\web\JsExpression('function(event, params){
                                                  $.post("' . Url::toRoute(["/fileinput/file-manager/sort-file", "id" => $this->id]) . '", {sort: params});
                                            }'),
                'filebatchselected' => new \yii\web\JsExpression('function(event, files) {                                               
                                                  $("#' . $this->id . '").fileinput("upload");
                                            }'),
            ], $this->pluginEvents),
        ]);
    }

    public function getWidgetHtml()
    {
        return $this->rawWidgetHtml;
    }

}
