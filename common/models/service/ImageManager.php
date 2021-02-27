<?php

namespace common\models\service;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%image_manager}}".
 *
 * @property int $id
 * @property string $name
 * @property string $class
 * @property int $item_id
 * @property int $sort
 * @property string $alt
 */
class ImageManager extends \yii\db\ActiveRecord {

    public $attachment;
   
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%image_manager}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'type'], 'required'],
            [['item_id', 'sort', 'size'], 'integer'],
            ['sort', 'default', 'value' => function($model) {
                $count = ImageManager::find()->andWhere(['class' => $model->class])->count();
                return ($count > 0) ? $count++ : 0;
            }],
            [['type','filetype'], 'safe'],
            [['name', 'class', 'alt', 'url'], 'string', 'max' => 256],
            //[['attachment'], 'image'],
           // [['attachment'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('art', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'class' => Yii::t('art', 'Class'),
            'item_id' => Yii::t('art', 'Item ID'),
            'alt' => Yii::t('art', 'Alt'),
            'type' => Yii::t('art', 'Type'),
            'size' => Yii::t('art', 'Size'),
        ];
    }

    /**
     * 
     * @return boolean
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            ImageManager::updateAllCounters(['sort' => -1], [
                'and', ['class' => $this->class, 'item_id' => $this->item_id], [ '>', 'sort', $this->sort]
            ]);
            //удаляем физически
                $dir = Yii::getAlias('@images') . '/';
                    if (file_exists($dir . $this->class . '/' . $this->name)) {
                        @unlink($dir . $this->class . '/' . $this->name);
                    }
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return string
     */
    public function getImageUrl() {
        if ($this->name) {
            $path = str_replace('admin', '', Url::home(true)) . 'uploads/images/' . $this->class . '/' . $this->name;
        } else {
            $path = str_replace('admin', '', Url::home(true)) . 'uploads/images/nophoto.svg';
        }
        return $path;
    }
    
}
