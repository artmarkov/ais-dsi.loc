<?php

namespace artsoft\auth\helpers;

use artsoft\models\User;
use common\models\service\UsersCard;
use Yii;
use yii\imagine\Image as Imagine;

class AvatarHelper
{

    /**
     *
     * @param \yii\web\UploadedFile $image
     * @return string
     */
    public static function saveAvatar($image)
    {
        $uploadPath = 'uploads/avatar';
        $extension = '.' . $image->extension;
        $fileName = 'avatar_' . Yii::$app->user->identity->id . '_' . time();
        $sourceFile = $uploadPath . '/' . $fileName . $extension;

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $image->saveAs($sourceFile);

        Imagine::$driver = [Imagine::DRIVER_GD2, Imagine::DRIVER_GMAGICK, Imagine::DRIVER_IMAGICK];
        $sizes = [
            'small' => 48,
            'medium ' => 96,
            'large' => 144,
        ];
        $avatars['orig'] = "/$sourceFile";
        foreach ($sizes as $alias => $size) {
            $avatarUrl = "$uploadPath/$fileName-{$size}x{$size}$extension";
            Imagine::thumbnail($sourceFile, $size, $size)->save($avatarUrl);
            $avatars[$alias] = "/$avatarUrl";
        }
        Yii::$app->user->identity->removeAvatar(); // удаляем старую аватарку
        Yii::$app->user->identity->setAvatars($avatars);
        UsersCard::setSigurPhoto($sourceFile);

        return $avatars;
    }

    public static function deleteAvatar($avatar)
    {
        if($avatar) {
            foreach (json_decode($avatar) as $item => $avatarUrl) {
                $avatarUrl = ltrim($avatarUrl, '/');
                if (file_exists($avatarUrl)) {
                    unlink($avatarUrl);
                }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}