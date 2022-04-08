<?php

namespace common\models\service;

use Yii;

/**
 * This is the model class for table "service_card_view".
 *
 * @property int|null $user_common_id
 * @property int|null $users_card_id
 * @property string|null $user_category
 * @property string|null $user_category_name
 * @property string|null $user_name
 * @property string|null $phone
 * @property string|null $phone_optional
 * @property string|null $email
 * @property int|null $status
 * @property string|null $key_hex
 * @property string|null $timestamp_deny
 * @property string|null $mode_main
 * @property string|null $mode_list
 * @property resource|null $photo_bin
 */
class ServiceCardView extends UsersCard
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_card_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['users_card_id'] = Yii::t('art/guide', 'Card ID');
        $attr['user_category'] = Yii::t('art/guide', 'Category');
        $attr['user_category_name'] = Yii::t('art/guide', 'Category Name');
        $attr['user_name'] = Yii::t('art', 'Username');
        $attr['phone'] = Yii::t('art/guide', 'Phone');
        $attr['phone_optional'] = Yii::t('art/guide', 'Phone Optional');
        $attr['email'] = Yii::t('art/student', 'Email');
        $attr['status'] = Yii::t('art', 'Status');

        return $attr;
    }
}
