<?php

namespace artsoft\settings\components;

/**
 * AttributeDetails.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class AttributeDetails
{
    public $field;
    public $group;
    public $key;
    public $language;
    public $multilingual;

    public function __construct($field, $group, $key = NULL, $language = NULL)
    {
        $this->field = $field;
        $this->group = $group;
        $this->key = ($key === NULL) ? $field : $key;
        $this->language = $language;
        $this->multilingual = ($language === NULL) ? FALSE : TRUE;
    }
}