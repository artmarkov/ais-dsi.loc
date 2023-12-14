<?php

namespace common\models\user;

use artsoft\models\User;
use Yii;

/**
 * This is the model class for table "users_view".
 *
 * @property int|null $id
 * @property string|null $username
 * @property string|null $email
 * @property int|null $email_confirmed
 * @property int|null $superadmin
 * @property string|null $registration_ip
 * @property string|null $roles
 * @property int|null $status
 * @property int|null $user_common_id
 * @property string|null $user_category_name
 * @property string|null $user_name
 * @property string|null $phone
 * @property string|null $phone_optional
 * @property int|null $user_common_status
 */
class UsersView extends User
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_view';
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['user_common_id'] = Yii::t('art', 'ID');
        $attr['user_category_name'] = Yii::t('art', 'User Category');
        $attr['user_name'] = Yii::t('art', 'Full Name');
        $attr['last_name'] = Yii::t('art', 'Last Name');
        $attr['first_name'] = Yii::t('art', 'First Name');
        $attr['middle_name'] = Yii::t('art', 'Middle Name');
        $attr['phone'] = Yii::t('art', 'Phone');
        $attr['phone_optional'] =  Yii::t('art', 'Phone Optional');
        $attr['user_common_status'] = Yii::t('art', 'Status');
        $attr['roles'] = Yii::t('art', 'Roles');

        return $attr;
    }
}
