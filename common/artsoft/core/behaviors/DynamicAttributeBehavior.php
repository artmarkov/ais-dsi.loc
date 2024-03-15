<?php

namespace artsoft\behaviors;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\StringHelper;
use dosamigos\transliterator\TransliteratorHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Генерирует уникальное имя атрибута из названия поля атрибута в динамической модели
 *
 * Class DynamicAttributeBehavior
 * @package artsoft\behaviors
 */
class DynamicAttributeBehavior extends Behavior
{
    public $in_attribute = 'name';
    public $out_attribute = 'attr';
    private $suf_array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'getAttr'
        ];
    }

    public function getAttr($event)
    {
        if (empty($this->owner->{$this->out_attribute})) {
            $this->owner->{$this->out_attribute} = $this->generateAttr($this->owner->{$this->in_attribute});
        } else {
            $this->owner->{$this->out_attribute} = $this->generateAttr($this->owner->{$this->out_attribute});
        }
    }

    private function generateAttr($attr)
    {
        $attr = $this->attribute($attr);
        if ($this->checkUniqueAttr($attr)) {
            return $attr;
        } else {
            for ($suffix = 0; !$this->checkUniqueAttr($new_attr = $attr . '' . $this->suf_array[$suffix]); $suffix++) {
            }
            return $new_attr;
        }
    }

    private function attribute($string, $replacement = '', $lowercase = true)
    {
        return  ArtHelper::slug($string, $replacement, $lowercase);
    }

    private function checkUniqueAttr($attr)
    {
        $pk = $this->owner->primaryKey();
        $pk = $pk[0];

        $condition = $this->out_attribute . ' = :out_attribute';
        $params = [':out_attribute' => $attr];
        if (!$this->owner->isNewRecord) {
            $condition .= ' and ' . $pk . ' != :pk';
            $params[':pk'] = $this->owner->{$pk};
        }

        return !$this->owner->find()
            ->where($condition, $params)
            ->one();
    }
}