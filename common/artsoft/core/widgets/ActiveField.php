<?php

namespace artsoft\widgets;

use Yii;

/**
 * @inheritdoc
 */
class ActiveField extends \yii\bootstrap\ActiveField
{
    public $language = NULL;

    public $multilingual = FALSE;
    
    /**
     * @var string the template for checkboxes in default layout
     */
    public $checkboxTemplate = "<div class=\"col-sm-3\"></div><div class=\"col-sm-9\"><div class=\"checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div></div>";
    
    /**
     * @var string the template for checkboxes in horizontal layout
     */
    public $horizontalCheckboxTemplate = "{beginWrapper}\n<div class=\"checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
   

    public function init()
    {

        parent::init();
        $this->template =   "<div class=\"col-sm-3\">{label}</div><div class=\"col-sm-9\">{input}\n{hint}\n{error}</div>";

        $languages = Yii::$app->art->languages;
        $isCurrentLanguage = (Yii::$app->language == $this->language);

        if ($this->language !== NULL && ($this->model->isMultilingual() || $this->multilingual)) {
            $languageLabel = $languages[$this->language];
            $inputLabel = $this->model->getAttributeLabel($this->attribute) . ((count($languages) > 1) ? " [$languageLabel]" : '');

            $this->labelOptions = array_merge($this->labelOptions, [
                'label' => $inputLabel
            ]);

            $this->options = array_merge($this->options, [
                'data-toggle' => 'multilang',
                'data-lang' => $this->language,
                'class' => ($isCurrentLanguage ? 'in' : ''),
            ]);

            $langPart = strtolower(str_replace('-', '_', $this->language));
            $this->attribute .= ($isCurrentLanguage) ? '' : '_' . $langPart;
        }
    }

}