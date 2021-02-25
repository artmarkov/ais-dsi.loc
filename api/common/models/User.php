<?php

namespace api\common\models;

/**
 * User model
 *
 * @property integer $id
 * @property string $email
 * @property string $info
 */
class User extends \artsoft\models\User 
{ 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['info', 'string'],
            [['last_name', 'first_name', 'middle_name'], 'string', 'max' => 124],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith('tokens t')
            ->andWhere(['t.token' => $token])
            ->andWhere(['>', 't.expired_at', time()])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    /**
     * 
     * @return array
     */
    public function fields()
    {
        return [
            'id' => 'id',
            'username' => 'username',
            'email' => 'email',
            'info' => 'info',
            'last_name' => 'last_name', 
            'first_name' =>'first_name', 
            'middle_name' =>'middle_name',
        ];
    }
}
