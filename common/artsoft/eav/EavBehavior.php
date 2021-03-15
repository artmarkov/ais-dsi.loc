<?php

namespace artsoft\eav;

use artsoft\eav\EavModel;
use artsoft\eav\models\EavAttribute;
use artsoft\eav\models\EavEntity;
use artsoft\eav\models\EavEntityModel;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;
use artsoft\db\ActiveRecord;

/**
 * Class EavBehavior
 * @package artsoft\eav
 *
 * @mixin ActiveRecord
 * @property EavModel $eavModel;
 * @property ActiveRecord $owner
 */
class EavBehavior extends Behavior
{
    protected $eavAttributes;
    protected $ownerClassName;
    protected $ownerPrimaryKey;
    protected $isAttributesLoaded;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_INIT => 'eavInit',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function attach($owner)
    {
        /** @var ActiveRecord $owner */
        parent::attach($owner);

        /** @var ActiveRecord $className */
        $className = $this->ownerClassName = get_class($this->owner);
        $this->ownerPrimaryKey = $className::primaryKey()[0];
        $this->eavAttributes = new \stdClass();
    }

    protected function getEavCatgoryId()
    {
        $owner = $this->owner;
        $category = $owner::getEavCategoryField();

        return ($category) ? $this->owner->{$category} : null;
    }

    public function eavInit()
    {
        $owner = $this->owner;
        $category = $owner::getEavCategoryField();
        $categoryId = $this->getEavCatgoryId();

        if (!$category || $categoryId) {
            $this->loadEavAttributes();
        }
    }

    public function afterFind()
    {
        $categoryId = $this->getEavCatgoryId();

        if ($categoryId) {
            $this->loadEavAttributes();
        }
    }

    protected function loadEavAttributes()
    {
        if (!$this->isAttributesLoaded) {
            $modelTable = EavEntityModel::tableName();
            $entityTable = EavEntity::tableName();
            $attributeTable = EavAttribute::tableName();
            $entityToAttributeTable = '{{%eav_entity_attribute}}';

            $attributes = EavAttribute::find()
                ->innerJoin($entityToAttributeTable, "$attributeTable.id = $entityToAttributeTable.attribute_id")
                ->innerJoin($entityTable, "$entityToAttributeTable.entity_id = $entityTable.id")
                ->innerJoin($modelTable, "$entityTable.model_id = $modelTable.id")
                ->andWhere(["$modelTable.entity_model" => $this->ownerClassName])
                ->andWhere(["$entityTable.category_id" => $this->getEavCatgoryId()])
                ->orderBy(['order' => SORT_DESC])
                ->all();

            $validators = $this->owner->getValidators();

            foreach ($attributes as $attribute) {
                $this->eavAttributes->{$attribute['name']} = $attribute;
                if ($attribute->required){
                $validators[] = \yii\validators\Validator::createValidator('required', $this->owner, [$attribute['name']], []);
                }
                else {
                $validators[] = \yii\validators\Validator::createValidator('save', $this->owner, [$attribute['name']], []);
                    
                }
            }

            $this->isAttributesLoaded = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name)
        || $this->hasEavAttribute($name);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->hasEavAttribute($name);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if ($this->hasEavAttribute($name)) {
                return $this->getEavAttributeValue($name);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            if ($this->hasEavAttribute($name)) {
                $this->setEavAttributeValue($name, $value);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (!parent::__isset($name)) {
            return $this->hasEavAttribute($name);
        } else {
            return true;
        }
    }

    /**
     * Whether an attribute exists
     * @param string $name the name of the attribute
     * @return boolean
     */
    public function hasEavAttribute($name)
    {
        return isset($this->eavAttributes->{$name});
    }

    /**
     * @param string $name the name of the attribute
     * @return string the attribute value
     */
    public function getEavAttribute($name)
    {
        if ($this->hasEavAttribute($name)) {
            return $this->eavAttributes->{$name};
        }

        return null;
    }

    /**
     * @param string $name the name of the attribute
     * @return string the attribute value
     */
    public function getEavAttributeValue($name)
    {
        if ($this->hasEavAttribute($name)) {
            $value = $this->eavAttributes->{$name}->getValue($this->ownerClassName, $this->getEavCatgoryId(), $this->owner->id);
            return ($value) ? $value->value : $this->eavAttributes->{$name}->default_value;
        }

        return null;
    }

    /**
     *
     * @param type $eavAttribute
     */
    public function setEavAttribute($eavAttribute)
    {
        $this->eavAttributes->{$name} = $eavAttribute;
    }

    /**
     * @param string $name the name of the attribute
     * @param string $value the value of the attribute
     */
    public function setEavAttributeValue($name, $value)
    {
        $this->eavAttributes->{$name}->setValue($this->ownerClassName, $this->getEavCatgoryId(), $this->owner->id, $value);
    }

    public function afterInsert()
    {
        foreach ($this->eavAttributes as $name => $attribute) {

            //Update eavValue item_id after owner insertion
            $attributeValue = $attribute->getValue($this->ownerClassName, $this->getEavCatgoryId(), null);
            $attributeValue->item_id = $this->owner->getPrimaryKey();
            $attributeValue->save();

            $attribute->save();
        }
    }

    public function afterUpdate()
    {
        foreach ($this->eavAttributes as $name => $attribute) {
            $attribute->save();  
        }
    }
    
    public function beforeDelete() {
        $attributes = models\EavValue::find()
                ->andWhere(['item_id' => $this->owner->id])
                ->all();
        //print_r($attributes);
           foreach ($attributes as  $attribute) {
               $attribute->delete();
           }
       }

    public function getEavAttributes()
    {
        return array_keys(get_object_vars($this->eavAttributes));
    }
}