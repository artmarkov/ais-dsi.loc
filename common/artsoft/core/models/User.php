<?php

namespace artsoft\models;

use artsoft\Art;
use artsoft\auth\helpers\AvatarHelper;
use artsoft\behaviors\DateToTimeBehavior;
use artsoft\helpers\AuthHelper;
use artsoft\helpers\ArtHelper;
use artsoft\traits\DateTimeTrait;
use artsoft\user\controllers\DefaultController;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int|null $email_confirmed
 * @property int|null $superadmin
 * @property string|null $registration_ip
 * @property string|null $bind_to_ip
 * @property string|null $confirmation_token
 * @property string|null $avatar
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property UserCommon $userCommon
 */
class User extends UserIdentity
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_BANNED = -1;
    const SCENARIO_NEW_USER = 'newUser';

    /**
     * @var string
     */
    public $gridRoleSearch;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $repeat_password;
    public $userCategory;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->art->user_table;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username'], 'unique'],
            [['username', 'email', 'bind_to_ip'], 'trim'],
            ['email', 'email'],
            [['status', 'email_confirmed'], 'integer'],
            ['bind_to_ip', 'validateBindToIp'],
            ['bind_to_ip', 'string', 'max' => 255],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            ['password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'string', 'max' => 255, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'string', 'min' => 6, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'trim', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['repeat_password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
            ['userCategory', 'safe'],
        ];
    }


    /**
     * Store result in session to prevent multiple db requests with multiple calls
     *
     * @param bool $fromSession
     *
     * @return static
     */
    public static function getCurrentUser($fromSession = true)
    {
        if (!$fromSession) {
            return static::findOne(Yii::$app->user->id);
        }

        $user = Yii::$app->session->get('__currentUser');

        if (!$user) {
            $user = static::findOne(Yii::$app->user->id);

            Yii::$app->session->set('__currentUser', $user);
        }

        return $user;
    }

    /**
     * Assign role to user
     *
     * @param int $userId
     * @param string $roleName
     *
     * @return bool
     */
    public static function assignRole($userId, $roleName)
    {
        try {
            Yii::$app->db->createCommand()
                ->insert(Yii::$app->art->auth_assignment_table, [
                    'user_id' => $userId,
                    'item_name' => $roleName,
                    'created_at' => time(),
                ])->execute();

            AuthHelper::invalidatePermissions();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assign roles to user
     *
     * @param int $userId
     * @param array $roles
     *
     * @return bool
     */
    public function assignRoles(array $roles)
    {
        foreach ($roles as $role) {
            User::assignRole($this->id, $role);
        }
    }

    /**
     * Revoke role from user
     *
     * @param int $userId
     * @param string $roleName
     *
     * @return bool
     */
    public static function revokeRole($userId, $roleName)
    {
        $result = Yii::$app->db->createCommand()
                ->delete(Yii::$app->art->auth_assignment_table, ['user_id' => $userId, 'item_name' => $roleName])
                ->execute() > 0;

        if ($result) {
            AuthHelper::invalidatePermissions();
        }

        return $result;
    }

    public static function getUsersByRole($roleName)
    {
        $table = Yii::$app->art->auth_assignment_table;
        $funcSql = <<< SQL
                SELECT user_id
                    FROM {$table}
                    WHERE item_name like any (string_to_array('{$roleName}', ',')::varchar[]) 
SQL;
        return Yii::$app->db->createCommand($funcSql)->queryColumn();
    }

    /**
     * @param string|array $roles
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasRole($roles, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }
        $roles = (array)$roles;

        AuthHelper::ensurePermissionsUpToDate();

        return array_intersect($roles, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROLES, [])) !== [];
    }

    /**
     * @param string $permission
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasPermission($permission, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }

        AuthHelper::ensurePermissionsUpToDate();

        return in_array($permission, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_PERMISSIONS, []));
    }

    /**
     * Useful for Menu widget
     *
     * <example>
     *    ...
     *        [ 'label'=>'Some label', 'url'=>['/site/index'], 'visible'=>User::canRoute(['/site/index']) ]
     *    ...
     * </example>
     *
     * @param string|array $route
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function canRoute($route, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin AND Art::isBackend()) {
            return true;
        }

        $baseRoute = AuthHelper::unifyRoute($route);

        if (substr($baseRoute, 0, 4) === "http") {
            return true;
        }

        if (Route::isFreeAccess($baseRoute)) {
            return true;
        }

        AuthHelper::ensurePermissionsUpToDate();

        return Route::isRouteAllowed($baseRoute, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROUTES, []));
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
            self::STATUS_BANNED => Yii::t('art', 'Banned'),
        );
    }

    /**
     * getUsersList
     *
     * @return array
     */
    public static function getUsersList()
    {
        $users = static::find()->select(['id', 'username'])
            ->where(['=', 'status', User::STATUS_ACTIVE])
            ->andWhere(['=', 'superadmin',0])
            ->asArray()->all();
        return ArrayHelper::map($users, 'id', 'username');
    }

    public static function getUsersByIds($ids = [])
    {
        $query = (new Query())->from('users_view')
            ->select('id , user_name as name')
            ->where(['=', 'status', User::STATUS_ACTIVE])
            ->andWhere(['id' => $ids])
            ->all();
        return\yii\helpers\ArrayHelper::map($query, 'id', 'name');
    }

    /**
     * getUsersList
     *
     * @return array
     */
    public static function getUsersListByCategory($category = [])
    {
        $users = static::find()->select([
            'users.id as id',
            'CONCAT(last_name, \' \',first_name, \' \',middle_name) as fullname',
            'user_common.user_category as category',
            'CASE
                  WHEN (user_category = \'employees\') THEN \'' . UserCommon::getUserCategoryValue('employees') . '\'
                  WHEN (user_category = \'teachers\') THEN \'' . UserCommon::getUserCategoryValue('teachers') . '\'
                  WHEN (user_category = \'students\') THEN \'' . UserCommon::getUserCategoryValue('students') . '\'
                  WHEN (user_category = \'parents\') THEN \'' . UserCommon::getUserCategoryValue('parents') . '\'
                  ELSE \'\'
            END as category_name'
        ])
            ->innerJoin('user_common', "user_common.user_id = users.id")
            ->where(['in', 'user_common.user_category', $category])
            ->andWhere(['=', 'users.status', self::STATUS_ACTIVE])
            ->orderBy('category_name, fullname')
            ->asArray()->all();
        return ArrayHelper::map($users, 'id', 'fullname', 'category_name');
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Check that there is no such confirmed E-mail in the system
     */
    public function validateEmailUnique()
    {
        if ($this->email) {
            $exists = User::findOne(['email' => $this->email]);

            if ($exists AND $exists->id != $this->id) {
                $this->addError('email', Yii::t('art', 'This e-mail already exists'));
            }
        }
    }

    /**
     * Validate bind_to_ip attr to be in correct format
     */
    public function validateBindToIp()
    {
        if ($this->bind_to_ip) {
            $ips = explode(',', $this->bind_to_ip);

            foreach ($ips as $ip) {
                if (!filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    $this->addError('bind_to_ip', Yii::t('art', "Wrong format. Enter valid IPs separated by comma"));
                }
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'username' => Yii::t('art', 'Login'),
            'superadmin' => Yii::t('art', 'Superadmin'),
            'confirmation_token' => Yii::t('art', 'Confirmation Token'),
            'registration_ip' => Yii::t('art', 'Registration IP'),
            'bind_to_ip' => Yii::t('art', 'Bind to IP'),
            'status' => Yii::t('art', 'Status'),
            'role' => Yii::t('art', 'Role'),
            'gridRoleSearch' => Yii::t('art', 'Roles'),
            'password' => Yii::t('art', 'Password'),
            'repeat_password' => Yii::t('art', 'Repeat password'),
            'email' => Yii::t('art', 'E-mail'),
            'email_confirmed' => Yii::t('art', 'E-mail confirmed'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['name' => 'item_name'])
            ->viaTable(Yii::$app->art->auth_assignment_table, ['user_id' => 'id']);
    }

    /**
     * Make sure user will not deactivate himself and superadmin could not demote himself
     * Also don't let non-superadmin edit superadmin
     *
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            if (php_sapi_name() != 'cli') {
                $this->registration_ip = ArtHelper::getRealIp();
            }
            $this->generateAuthKey();
        } else {
            // Console doesn't have Yii::$app->user, so we skip it for console
            if (php_sapi_name() != 'cli') {
                if (Yii::$app->user->id == $this->id) {
                    // Make sure user will not deactivate himself
                    // $this->status = static::STATUS_ACTIVE; //Пользователь деактивирует себя при изменении e-mail - Model ProfileForm
                    // Superadmin could not demote himself
                    if (Yii::$app->user->isSuperadmin AND $this->superadmin != 1) {
                        $this->superadmin = 1;
                    }
                }

                // Don't let non-superadmin edit superadmin
                if (!Yii::$app->user->isSuperadmin AND $this->oldAttributes['superadmin'] == 1
                ) {
                    return false;
                }
            }
        }

        // If password has been set, than create password hash
        if ($this->password) {
            $this->setPassword($this->password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * Don't let delete yourself and don't let non-superadmin delete superadmin
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // Console doesn't have Yii::$app->user, so we skip it for console
        if (php_sapi_name() != 'cli') {
            // Don't let delete yourself
            if (Yii::$app->user->id == $this->id) {
                return false;
            }

            // Don't let non-superadmin delete superadmin
            if (!Yii::$app->user->isSuperadmin AND $this->superadmin == 1) {
                return false;
            }
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }


    /**
     * @param string $size
     * @return boolean|string
     */
    public function getAvatar($size = 'small')
    {
        if (!empty($this->avatar)) {
            $avatars = json_decode($this->avatar);

            if (isset($avatars->$size)) {
                return $avatars->$size;
            }
        }

        return false;
    }

    /**
     *
     * @param array $avatars
     */
    public function setAvatars($avatars)
    {
        $this->avatar = json_encode($avatars);
        return $this->save();
    }

    /**
     *
     */
    public function removeAvatar()
    {
        AvatarHelper::deleteAvatar($this->avatar);
        $this->avatar = '';
        return $this->save();
    }

    /**
     * Gets query for [[UserCommon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCommon()
    {
        return $this->hasOne(UserCommon::class, ['user_id' => 'id']);
    }

    public function isAdmin()
    {
        return $this->hasRole('administrator');
    }
}
