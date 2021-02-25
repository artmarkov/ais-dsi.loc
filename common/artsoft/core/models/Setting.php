<?php

namespace artsoft\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string $key
 * @property string $group
 * @property string $language
 * @property string $value
 * @property string $description
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class Setting extends \artsoft\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value', 'language'], 'string'],
            [['key', 'group'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => Yii::t('art', 'Key'),
            'group' => Yii::t('art', 'Group'),
            'value' => Yii::t('art', 'Value'),
            'language' => Yii::t('art', 'Language'),
        ];
    }

    /**
     * Get setting by group and key
     *
     * @param type $group
     * @param type $key
     * @return type
     */
    public static function getSetting($group, $key, $language = NULL)
    {
        return self::findOne(['group' => $group, 'key' => $key, 'language' => $language]);
    }

}
