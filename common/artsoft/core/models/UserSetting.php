<?php

namespace artsoft\models;

use Yii;

/**
 * This is the model class for table "user_setting".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $key
 * @property string $value
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class UserSetting extends \artsoft\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'key', 'value'], 'required'],
            [['user_id'], 'integer'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }
}
