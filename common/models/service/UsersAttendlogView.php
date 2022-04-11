<?php

namespace common\models\service;

use Yii;

/**
 * This is the model class for table "users_attendlog_view".
 *
 * @property int $id
 * @property int $users_attendlog_id
 * @property int|null $user_common_id
 * @property string|null $user_name
 * @property string|null $user_category
 * @property string|null $user_category_name
 * @property int|null $auditory_id
 * @property int|null $timestamp_received
 * @property int|null $timestamp_over
 * @property int|null $timestamp
 */
class UsersAttendlogView extends UsersAttendlog
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_attendlog_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['user_category'] = Yii::t('art/guide', 'Category');
        $attr['user_category_name'] = Yii::t('art/guide', 'Category Name');
        $attr['user_name'] = Yii::t('art', 'Username');
        $attr['timestamp'] = Yii::t('art', 'Timestamp');

        return $attr;
    }
}
