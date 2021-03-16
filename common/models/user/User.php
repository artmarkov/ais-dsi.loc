<?php

namespace common\models\user;

use artsoft\behaviors\DateToTimeBehavior;
use artsoft\helpers\AuthHelper;
use artsoft\helpers\artHelper;
use artsoft\models\Role;
use artsoft\models\Route;
use artsoft\models\UserIdentity;
use artsoft\models\UserQuery;
use artsoft\traits\DateTimeTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $email_confirmed
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property string $bind_to_ip
 * @property string $registration_ip
 * @property integer $status
 * @property integer $superadmin
 * @property string $avatar
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends UserIdentity {

    use DateTimeTrait;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_BANNED = -1;
    const SCENARIO_NEW_USER = 'newUser';
    const GENDER_NOT_SET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const USER_CATEGORY_STAFF = 1;
    const USER_CATEGORY_TEACHER = 2;
    const USER_CATEGORY_STUDENT = 3;
    const USER_CATEGORY_PARENT = 4;

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

    /**
     * @var string
     */
    public $birth_date;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->art->user_table;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            BlameableBehavior::className(),
            TimestampBehavior::className(),
            [
                'class' => DateToTimeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'birth_date',
                    ActiveRecord::EVENT_AFTER_FIND => 'birth_date',
                ],
                'timeAttribute' => 'birth_timestamp',
                'timeFormat' => 'd-m-Y',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'email', 'birth_date'], 'required'],
            ['birth_timestamp', 'safe'],
            ['username', 'unique'],
            [['username', 'email', 'bind_to_ip'], 'trim'],
            ['email', 'email'],
            [['status', 'user_category', 'email_confirmed'], 'integer'],
            ['bind_to_ip', 'validateBindToIp'],
            ['bind_to_ip', 'string', 'max' => 255],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            ['snils', 'string', 'max' => 16],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            ['bind_to_ip', 'string', 'max' => 255],
            ['info', 'string', 'max' => 1024],
            ['gender', 'integer'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            ['password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'string', 'max' => 255, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'string', 'min' => 6, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['password', 'trim', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['repeat_password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
            ['user_category', 'default', 'value' => self::USER_CATEGORY_STAFF],
            ['user_category', 'in', 'range' => [self::USER_CATEGORY_STAFF, self::USER_CATEGORY_TEACHER, self::USER_CATEGORY_STUDENT, self::USER_CATEGORY_PARENT]],
            ['birth_date', 'date', 'format' => 'dd-MM-yyyy'],
        ];
    }

    /* Геттер для полного имени человека */

    public function getFullName() {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }

    /**
     * Store result in session to prevent multiple db requests with multiple calls
     *
     * @param bool $fromSession
     *
     * @return static
     */
    public static function getCurrentUser($fromSession = true) {
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
    public static function assignRole($userId, $roleName) {
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
    public function assignRoles(array $roles) {
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
    public static function revokeRole($userId, $roleName) {
        $result = Yii::$app->db->createCommand()
                        ->delete(Yii::$app->art->auth_assignment_table, ['user_id' => $userId, 'item_name' => $roleName])
                        ->execute() > 0;

        if ($result) {
            AuthHelper::invalidatePermissions();
        }

        return $result;
    }

    /**
     * @param string|array $roles
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasRole($roles, $superAdminAllowed = true) {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }
        $roles = (array) $roles;

        AuthHelper::ensurePermissionsUpToDate();

        return array_intersect($roles, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROLES, [])) !== [];
    }

    /**
     * @param string $permission
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasPermission($permission, $superAdminAllowed = true) {
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
    public static function canRoute($route, $superAdminAllowed = true) {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
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
    public static function getStatusList() {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
            self::STATUS_BANNED => Yii::t('art', 'Banned'),
        );
    }

    /**
     * Get gender list
     * @return array
     */
    public static function getGenderList() {
        return array(
            self::GENDER_NOT_SET => Yii::t('yii', '(not set)'),
            self::GENDER_MALE => Yii::t('art', 'Male'),
            self::GENDER_FEMALE => Yii::t('art', 'Female'),
        );
    }

    /**
     * Get gender list
     * @return array
     */
    public static function getUserCategoryList() {
        return array(
            self::USER_CATEGORY_STAFF => Yii::t('art', 'Staff'),
            self::USER_CATEGORY_TEACHER => Yii::t('art', 'Teacher'),
            self::USER_CATEGORY_STUDENT => Yii::t('art', 'Student'),
            self::USER_CATEGORY_PARENT => Yii::t('art', 'Parent'),
        );
    }

    /**
     * getUsersList
     *
     * @return array
     */
    public static function getUsersList() {
        $users = static::find()->select(['id', 'username'])->asArray()->all();
        return ArrayHelper::map($users, 'id', 'username');
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val) {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getFunctionValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getUserCategoryValue($val) {
        $ar = self::getUserCategoryList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Check that there is no such confirmed E-mail in the system
     */
    public function validateEmailUnique() {
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
    public function validateBindToIp() {
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
    public function attributeLabels() {
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
            'email_confirmed' => Yii::t('art', 'E-mail confirmed'),
            'email' => Yii::t('art', 'E-mail'),
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'gender' => Yii::t('art', 'Gender'),
            'info' => Yii::t('art', 'Short Info'),
            'snils' => Yii::t('art', 'Snils'),
            'birth_timestamp' => Yii::t('art', 'Birth Date'),
            'birth_date' => Yii::t('art', 'Birth Date'),
            'user_category' => Yii::t('art', 'User Category'),
            'fullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles() {
        return $this->hasMany(Role::className(), ['name' => 'item_name'])
                        ->viaTable(Yii::$app->art->auth_assignment_table, ['user_id' => 'id']);
    }

    /**
     * Make sure user will not deactivate himself and superadmin could not demote himself
     * Also don't let non-superadmin edit superadmin
     *
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            if (php_sapi_name() != 'cli') {
                $this->registration_ip = artHelper::getRealIp();
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
    public function beforeDelete() {
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
    public static function find() {
        return new UserQuery(get_called_class());
    }


    /**
     * @param string $size
     * @return boolean|string
     */
    public function getAvatar($size = 'small') {
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
    public function setAvatars($avatars) {
        $this->avatar = json_encode($avatars);
        return $this->save();
    }

    /**
     *
     */
    public function removeAvatar() {
        $this->avatar = '';
        return $this->save();
    }

    /**
     * Первая буква заглавная
     */
    protected function getUcFirst($str, $encoding = 'UTF-8') {
        /* $str = mb_ereg_replace('^[\ ]+', '', $str);
          $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
          mb_substr($str, 1, mb_strlen($str), $encoding); */
        $str = mb_convert_case($str, MB_CASE_TITLE, $encoding);
        return $str;
    }

    /**
     * До валидации формируем строки с первой заглавной
     */
    public function beforeValidate() {

        $this->first_name = User::getUcFirst($this->first_name);
        $this->middle_name = User::getUcFirst($this->middle_name);
        $this->last_name = User::getUcFirst($this->last_name);


        return parent::beforeValidate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(self::className(), ['id' => 'created_by']);
    } /**
 * @return \yii\db\ActiveQuery
 */
    public function getUpdatedBy()
    {
        return $this->hasOne(self::className(), ['id' => 'updated_by']);
    }
}
