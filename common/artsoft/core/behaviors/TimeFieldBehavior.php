<?php

namespace artsoft\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * Class TimeFieldBehavior
 *
 * ~~~
 * use artsoft\behaviors\DateFieldBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => DateFieldBehavior::className(),
 *             'attributes' => ['attribute1', 'attribute2'],
 *             'timeFormat' => 'some value',
 *             'defaultEncodedValue' => 'some value',
 *             'defaultDecodedValue' => 'some value',
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @property BaseActiveRecord $owner
 *
 * @package https://github.com/frostealth/yii2-array-field.git
 */
class TimeFieldBehavior extends Behavior
{
    /**
     * @var array
     */
    public $attributes = [];
    /**
     * @var string
     */
    public $timeFormat = 'H:i';

    /**
     * @var mixed
     */
    public $defaultEncodedValue = null;

    /**
     * @var mixed
     */
    public $defaultDecodedValue = null;

    /**
     * @var array
     */
    private $_cache = [];

    /**
     * @var array
     */
    private $_oldAttributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'decode',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'decode',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'decode',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encode',
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'encode',
        ];
    }

    /**
     * Encode attributes
     */
    public function encode()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_oldAttributes[$attribute])) {
                $this->owner->setOldAttribute($attribute, $this->_oldAttributes[$attribute]);
            }

            $value = $this->owner->getAttribute($attribute);
            $this->_cache[$attribute] = $value;
            if($value) {
                $t = explode(":", $value);
                $value = mktime($t[0], $t[1], 0, 1, 1, 70);
            }
            else {
            $value = $this->defaultEncodedValue;
            }
            $this->owner->setAttribute($attribute, $value);
        }
    }

    /**
     * Decode attributes
     */
    public function decode()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_cache[$attribute])) {
                $value = $this->_cache[$attribute];
            } else {
                $value = null != $this->owner->getAttribute($attribute) ? date($this->timeFormat, $this->owner->getAttribute($attribute)) : null;
            }

            $value = $value ? $value : $this->defaultDecodedValue;
            $this->owner->setAttribute($attribute, $value);

            if (!$this->owner->getIsNewRecord()) {
                $this->_oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);
                $this->owner->setOldAttribute($attribute, $value);
            }
        }

        $this->_cache = [];
    }
}
