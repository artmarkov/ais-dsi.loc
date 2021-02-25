<?php

namespace artsoft\settings\models;

use artsoft\settings\components\AttributeDetails;
use Yii;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\validators\Validator;

/**
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class BaseSettingsModel extends Model
{
    /**
     * Array with fields descriptions
     *
     * @var array
     */
    private $_descriptions = [];

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->initAttributeValues();
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     *
     * @inheritdoc
     */
    public function attributes()
    {
        $attributes = $this->getAttributesDetails();
        return array_keys($attributes);
    }

    /**
     *
     * @inheritdoc
     */
    public function safeAttributes()
    {
        return $this->attributes();
    }

    /**
     * Save settings to database
     */
    public function save()
    {
        $attributes = $this->getAttributesDetails();
        foreach ($attributes as $attribute) {
            $field = $this->getMultilingualFieldName($attribute->field);
            Yii::$app->settings->set([$attribute->group, $attribute->key, $attribute->language], $this->{$field});
        }
    }

    /**
     * Init values of attributes
     */
    protected function initAttributeValues()
    {
        $attributes = $this->getAttributesDetails();
        foreach ($attributes as $attribute) {
            $this->{$attribute->field} = Yii::$app->settings->getFromDB($attribute->group, $attribute->key, $attribute->language);
        }

        foreach ($this->rules() as $rule) {
            if (is_array($rule) && isset($rule[0], $rule[1]) && $rule[1] == 'default') { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array)$rule[0], array_slice($rule, 2));
                $validator->validateAttribute($this, $rule[0]);
            }
        }

    }

    /**
     * Converts `title_en-US` to `title_en_us`
     *
     * @param string $attribute
     * @return string
     */
    protected function getMultilingualFieldName($attribute)
    {
        return Inflector::camel2id(Inflector::id2camel($attribute), "_");
    }

    /**
     * Generate list of attributes details object
     */
    protected function getAttributesDetails()
    {
        $attributes = [];
        $group = $this->getGroup();
        $languages = array_keys(Yii::$app->art->languages);
        $modelAttributes = parent::attributes();
        $multilingualAttributes = ($this->isMultilingual()) ? $this->getBehavior('multilingualSettings')->attributes : [];

        foreach ($modelAttributes as $attribute) {
            if (in_array($attribute, $multilingualAttributes)) {
                foreach ($languages as $language) {
                    $field = ($language == Yii::$app->language) ? $attribute : $this->getMultilingualFieldName("{$attribute}_{$language}");
                    $attributes[$field] = new AttributeDetails($field, $group, $attribute, $language);
                }
            } else {
                $attributes[$attribute] = new AttributeDetails($attribute, $group);
            }
        }

        return $attributes;
    }

    /**
     * Get setting field description
     *
     * @param string $key
     */
    public function getDescription($key)
    {
        return (isset($this->_descriptions[$key])) ? $this->_descriptions[$key] : '';
    }

    /**
     * Get settings group
     */
    public function getGroup()
    {
        return defined('static::GROUP') ? static::GROUP : NULL;
    }

    /**
     * Whether has model multilingual behavior
     *
     * @return boolean
     */
    public function isMultilingual()
    {
        return ($this->getBehavior('multilingualSettings') !== NULL);
    }

}