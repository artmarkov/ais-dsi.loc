<?php

namespace artsoft\helpers;

use artsoft\models\User;

/**
 * @inheritdoc
 */
class Html extends \yii\helpers\Html
{

    /**
     * Hide link if user hasn't access to it
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {
        if (in_array($url, [null, '', '#'])) {
            return parent::a($text, $url, $options);
        }
        if (isset($options['visible']) && $options['visible'] == true) {
            return parent::a($text, $url, $options);
        }
        return User::canRoute($url) ? parent::a($text, $url, $options) : '';
    }

    /**
     *
     * @inheritdoc
     */
    public static function checkbox($name, $checked = false, $options = [])
    {
        $options['checked'] = (bool)$checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }

        $label = (isset($options['label'])) ? $options['label'] : ' ';
        $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
        unset($options['label'], $options['labelOptions']);
        $content = static::input('checkbox', $name, $value, $options) . static::label($label, null, $labelOptions);
        return '<div class="checkbox">' . $hidden . $content . '</div>';
    }

    /**
     * @param string $type
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string
     */
    protected static function booleanInput($type, $name, $checked = false, $options = [])
    {
        // 'checked' option has priority over $checked argument
        if (!isset($options['checked'])) {
            $options['checked'] = (bool)$checked;
        }
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hiddenOptions = [];
            if (isset($options['form'])) {
                $hiddenOptions['form'] = $options['form'];
            }
            // make sure disabled input is not sending any value
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = static::hiddenInput($name, $options['uncheck'], $hiddenOptions);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);
            $content = static::input($type, $name, $value, $options) . ' ' . static::label($label, null, $labelOptions);
            return $hidden . $content;
        }

        return $hidden . static::input($type, $name, $value, $options);
    }

    /**
     * Generates list of hidden input tags for the given model attribute when the attribute value is an array.
     *
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeHiddenInputList($model, $attribute, $options = [])
    {
        $str = '';
        $flattenedList = static::getflatInputNames($attribute, $model->$attribute);
        foreach ($flattenedList as $flattenAttribute) {
            $str .= static::activeHiddenInput($model, $flattenAttribute, $options);
        }
        return $str;
    }

    /**
     * @param string $name
     * @param array $values
     * @return array
     */
    private static function getflatInputNames($name, array $values)
    {
        $flattened = [];
        foreach ($values as $key => $val) {
            $nameWithKey = $name . '[' . $key . ']';
            if (is_array($val)) {
                $flattened += static::getflatInputNames($nameWithKey, $val);
            } else {
                $flattened[] = $nameWithKey;
            }
        }
        return $flattened;
    }
}
