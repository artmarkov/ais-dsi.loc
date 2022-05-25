<?php

namespace common\models\service;

use artsoft\auth\assets\AvatarAsset;
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
 * @property int $version
 * @property int $access_work Разрешение на доступ к работе получен
 *
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
            [['user_common_id'], 'unique', 'message' => 'Пропуск у данного пользователя уже задан.'],
            [['key_hex'], 'unique'],
            [['timestamp_deny', 'photo_bin'], 'safe'],
            [['photo_ver'], 'default', 'value' => 1],
            [['photo_ver', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'access_work_flag'], 'integer'],
            [['user_common_id'], 'string', 'max' => 4],
            [['key_hex'], 'string', 'max' => 8, 'min' => 8],
            [['key_hex'], 'match', 'pattern' =>'/^[0-9,A-Z]+$/', 'message' => 'Код пропуска должен содержать только символы [0-9,A-Z].'],
            [['mode_main'], 'string', 'max' => 127],
            [['mode_list'], 'string', 'max' => 512],
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
            'version' => Yii::t('art', 'Version'),
            'access_work_flag' => 'Разрешение на доступ к работе получено'
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
     * @return string
     */
    public function getSigurPhoto()
    {
        return is_resource($this->photo_bin) ? 'data:image/png;base64,' . stream_get_contents($this->photo_bin)  : AvatarAsset::getDefaultAvatar('large');
    }

    public function beforeSave($insert)
    {
        $this->timestamp_deny = $this->timestamp_deny ? Yii::$app->formatter->asDate($this->timestamp_deny, 'php:Y-m-d H:i:s') : '';
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->timestamp_deny = $this->timestamp_deny ? Yii::$app->formatter->asDate($this->timestamp_deny, 'php:d.m.Y H:i') : '';
        parent::afterFind();
    }

}
