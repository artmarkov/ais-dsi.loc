<?php

namespace common\models\sigur;

use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "users_card".
 *
 * @property int $id
 * @property string|null $user_common_id
 * @property string|null $key_hex Пропуск (в формате HEX)
 * @property string|null $timestamp_deny Срок действия в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС
 * @property string|null $mode_main Основной режим
 * @property string|null $mode_list Список режимов
 * @property resource|null $photo_bin Фотография 1
 * @property int|null $photo_ver Версия фотографии
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Users $createdBy0
 */
class UsersCard extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_card';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id'], 'required'],
            [['timestamp_deny'], 'safe'],
            [['photo_bin'], 'string'],
            [['photo_ver'], 'default', 'value' => 1],
            [['photo_ver', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['user_common_id'], 'string', 'max' => 4],
            [['key_hex'], 'string', 'max' => 8],
            [['mode_main'], 'string', 'max' => 127],
            [['mode_list'], 'string', 'max' => 512],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'key_hex' => 'Пропуск',
            'timestamp_deny' => 'Действует до',
            'mode_main' => 'Основной режим',
            'mode_list' => 'Список режимов',
            'photo_bin' => 'Фотография',
            'photo_ver' => 'Версия фотографии',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Устанавливаем фото для СКУД СИГУР
     * вызываем из common/artsoft/auth/helpers/AvatarHelper.php
     * @return bool
     */
    public static function setSigurPhoto($sourceFile = null)
    {
        if ($sourceFile != null) {
            $user = User::findIdentity(Yii::$app->user->id);
            $userCommon = $user->userCommon;
            if ($userCommon) {
                $userCard = self::find()->where(new \yii\db\Expression("user_common_id::int = {$userCommon->id}"))->one() ?: new self;
                $userCard->user_common_id = $userCommon->id;
                $userCard->photo_bin = base64_encode(file_get_contents(Yii::getAlias($sourceFile)));
                $userCard->photo_ver++;
                return $userCard->save(false);
            }
        }
        return false;
    }

    /**
     * Gets query for [[CreatedBy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getCreatedBy0()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    public
    function beforeSave($insert)
    {
        $this->timestamp_deny = $this->timestamp_deny ? Yii::$app->formatter->asDate($this->timestamp_deny, 'php:Y-m-d H:i:s') : '';
        return parent::beforeSave($insert);
    }

    public
    function afterFind()
    {
        $this->timestamp_deny = $this->timestamp_deny ? Yii::$app->formatter->asDate($this->timestamp_deny, 'php:d.m.Y H:i') : '';
        parent::afterFind();
    }

}
