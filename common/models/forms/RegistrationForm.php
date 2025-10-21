<?php

namespace common\models\forms;

use artsoft\models\User;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use Yii;
use yii\base\Model;
use yii\db\Query;

class RegistrationForm extends Model
{
    const SCENARIO_FRONFEND = 'frontend';
    const SCENARIO_BACKEND = 'backend';

    public $student_first_name;
    public $student_middle_name;
    public $student_last_name;
    public $student_gender;
    public $student_birth_date;
    public $student_snils;

    public $relation_id;
    public $parent_first_name;
    public $parent_middle_name;
    public $parent_last_name;
    public $parent_gender;
    public $parent_birth_date;
    public $parent_snils;
    public $parent_address;

    public $email;
    public $phone;
    public $phone_optional;
    public $address;
    public $city;
    public $street;
    public $house;
    public $flat;

    public $student_sert_name;
    public $student_sert_series;
    public $student_sert_num;
    public $student_sert_organ;
    public $student_sert_date;

    public $parent_sert_name;
    public $parent_sert_series;
    public $parent_sert_num;
    public $parent_sert_organ;
    public $parent_sert_code;
    public $parent_sert_date;
    public $parent_sert_country;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required', 'on' => self::SCENARIO_FRONFEND],
            [['phone', 'city', 'street', 'house'], 'required'],
            [['student_first_name', 'student_last_name', 'student_birth_date'], 'required'],
            [['student_snils', 'parent_snils'], 'required'],
            [[ 'student_sert_num', 'parent_sert_num', 'parent_address'], 'required'],
            ['student_last_name', 'validateStudent'],
            [['parent_first_name', 'parent_last_name'], 'required'],
            [['relation_id'], 'required'],
            [['parent_birth_date'], 'required', 'on' => self::SCENARIO_FRONFEND],
            [['student_snils', 'parent_snils'], 'required', 'on' => self::SCENARIO_FRONFEND],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'trim'],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'trim'],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'string', 'max' => 124],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'string', 'max' => 124],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['student_birth_date', 'parent_birth_date'], 'date'],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            [['address'], 'string', 'max' => 1024],
            [['city', 'street', 'house', 'flat', 'email'], 'string', 'max' => 124],
            [['student_snils', 'parent_snils'], 'string', 'max' => 16],
            [['student_first_name', 'student_last_name', 'parent_first_name', 'parent_last_name'], 'string', 'min' => 3],
            // ['email', 'validateEmail'],
            // ['email', 'email'],
            [['student_sert_date'], 'date'],
            [['student_sert_name', 'student_sert_series', 'student_sert_num'], 'string', 'max' => 32],
            [['student_sert_organ'], 'string', 'max' => 127],
            [['student_sert_series', 'student_sert_num', 'student_sert_organ', 'student_sert_date'], 'required', 'on' => self::SCENARIO_FRONFEND],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['student_sert_num', 'student_sert_organ', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_series != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-student_sert_series').val() != NULL;
                    }"],
            [['student_sert_organ', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_num != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-student_sert_num').val() != NULL;
                    }"],
            [['student_sert_num', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_organ != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-student_sert_organ').val() != NULL;
                    }"],
            [['student_sert_num', 'student_sert_organ'], 'required', 'when' => function ($model) {
                return $model->student_sert_date != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-student_sert_date').val() != NULL;
                    }"],
            ['student_sert_date', 'default', 'value' => NULL],
            [['parent_sert_date'], 'date'],
            [['parent_sert_name', 'parent_sert_series', 'parent_sert_num', 'parent_sert_code'], 'string', 'max' => 32],
            [['parent_sert_organ', 'parent_sert_country'], 'string', 'max' => 127],
            [['parent_sert_series', 'parent_sert_num', 'parent_sert_organ', 'parent_sert_code', 'parent_sert_date'], 'required', 'on' => self::SCENARIO_FRONFEND],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['parent_sert_num', 'parent_sert_organ', 'parent_sert_code', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_series != NULL && $model->parent_sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_series').val() != NULL && $('#registrationform-sert_name').val() != 'pasport_foreign';
                    }"],
            [['parent_sert_series', 'parent_sert_organ', 'parent_sert_code', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_num != NULL && $model->parent_sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_num').val() != NULL && $('#registrationform-parent_sert_name').val() != 'pasport_foreign';
                    }"],
            [['parent_sert_series', 'parent_sert_num', 'parent_sert_code', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_organ != NULL && $model->parent_sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_organ').val() != NULL && $('#registrationform-parent_sert_name').val() != 'pasport_foreign';
                    }"],
            [['parent_sert_series', 'parent_sert_num', 'parent_sert_organ', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_code != NULL && $model->parent_sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_code').val() != NULL && $('#registrationform-parent_sert_name').val() != 'pasport_foreign';
                    }"],
            [['parent_sert_series', 'parent_sert_num', 'parent_sert_organ', 'parent_sert_code'], 'required', 'when' => function ($model) {
                return $model->parent_sert_date != NULL && $model->parent_sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_date').val() != NULL && $('#registrationform-parent_sert_name').val() != 'pasport_foreign';
                    }"],
            [['parent_sert_num', 'parent_sert_country'], 'required', 'when' => function ($model) {
                return $model->parent_sert_name == 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#registrationform-parent_sert_name').val() == 'pasport_foreign';
                    }"],
            ['parent_sert_date', 'default', 'value' => NULL],
            [['student_gender'], 'required'],
            [['student_gender', 'parent_gender'], 'integer'],


        ];
    }

    public function validateStudent($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $birth_date = Yii::$app->formatter->asTimestamp($this->student_birth_date);

            $user = (new Query())->from('students_view')
                ->select(['students_id'])
                ->where(['AND',
                    ['first_name' => $this->student_first_name],
                    ['last_name' => $this->student_last_name],
                ]);

            if ($this->student_middle_name != null) {
                $user = $user->andWhere(['like', 'middle_name', $this->student_middle_name]);
            }
            $user = $user->andWhere(['=', 'birth_date', $birth_date]);

            if ($user->exists()) {
                $this->addError($attribute, 'Ученик с введенными данными уже добавлен');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $this->email)) {
                $this->addError($attribute, 'Значение "E-mail" не является правильным адресом.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'student_first_name' => Yii::t('art', 'First Name'),
            'student_middle_name' => Yii::t('art', 'Middle Name'),
            'student_last_name' => Yii::t('art', 'Last Name'),
            'relation_id' => Yii::t('art/student', 'Relation'),
            'parent_first_name' => Yii::t('art', 'First Name'),
            'parent_middle_name' => Yii::t('art', 'Middle Name'),
            'parent_last_name' => Yii::t('art', 'Last Name'),
            'student_birth_date' => Yii::t('art', 'Birth Date'),
            'parent_birth_date' => Yii::t('art', 'Birth Date'),
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'student_snils' => Yii::t('art', 'Snils'),
            'parent_snils' => Yii::t('art', 'Snils'),
            'email' => Yii::t('art', 'E-mail'),
            'parent_address' => Yii::t('art/guide', 'Address'),
            'city' => Yii::t('art/guide', 'City'),
            'street' => Yii::t('art/guide', 'Street'),
            'house' => Yii::t('art/guide', 'House'),
            'flat' => Yii::t('art/guide', 'Flat'),
            'student_sert_name' => Yii::t('art/student', 'Sertificate Name'),
            'student_sert_series' => Yii::t('art/student', 'Sertificate Series'),
            'student_sert_num' => Yii::t('art/student', 'Sertificate Num'),
            'student_sert_organ' => Yii::t('art/student', 'Sertificate Organ'),
            'student_sert_date' => Yii::t('art/student', 'Sertificate Date'),
            'parent_sert_name' => Yii::t('art/parents', 'Sertificate Name'),
            'parent_sert_series' => Yii::t('art/parents', 'Sertificate Series'),
            'parent_sert_num' => Yii::t('art/parents', 'Sertificate Num'),
            'parent_sert_organ' => Yii::t('art/parents', 'Sertificate Organ'),
            'parent_sert_date' => Yii::t('art/parents', 'Sertificate Date'),
            'parent_sert_code' => Yii::t('art/parents', 'Sertificate Code'),
            'parent_sert_country' => Yii::t('art/parents', 'Sertificate Country'),
            'student_gender' => Yii::t('art', 'Gender'),
            'parent_gender' => Yii::t('art', 'Gender'),
        ];
    }

    protected function setAddress()
    {
        return implode(', ', ['г. ' . $this->city, 'улица ' . $this->street, 'дом. ' . $this->house, 'кв. ' . $this->flat]);
    }

    public function registration()
    {
        $userStudent = new User();
        $userCommonStudent = new UserCommon();
        $modelStudent = new Student();
        $modelDependence = new StudentDependence();

        $userCommonParent = $this->findParentByFio(); // Если родитель был уже зарегистрирован с другим ребенком
        $parentsAvailable = $userCommonParent->user ? true : false;
        $userParent = $userCommonParent->user ?? new User();
        $modelParent = Parents::findOne(['user_common_id' => $userCommonParent->id]) ?? new Parents();

        $userCommonStudent->setAttributes([
            'first_name' => $this->student_first_name,
            'middle_name' => $this->student_middle_name,
            'last_name' => $this->student_last_name,
            'birth_date' => $this->student_birth_date,
            'gender' => $this->student_gender,
            'phone' => $this->phone,
            'phone_optional' => $this->phone_optional,
            'snils' => $this->student_snils,
            'email' => $this->email,
            'address' => $this->setAddress(),
        ]);
        $modelStudent->setAttributes([
            'sert_name' => $this->student_sert_name,
            'sert_series' => $this->student_sert_series,
            'sert_num' => $this->student_sert_num,
            'sert_organ' => $this->student_sert_organ,
            'sert_date' => $this->student_sert_date,
        ]);
        if (!$parentsAvailable) {
            $userCommonParent->setAttributes([
                'first_name' => $this->parent_first_name,
                'middle_name' => $this->parent_middle_name,
                'last_name' => $this->parent_last_name,
                'birth_date' => $this->parent_birth_date,
                'gender' => $this->parent_gender,
                'phone' => $this->phone,
                'phone_optional' => $this->phone_optional,
                'snils' => $this->parent_snils,
                'email' => $this->email,
                'address' => $this->parent_address,
            ]);
            $modelParent->setAttributes([
                'sert_name' => $this->parent_sert_name,
                'sert_series' => $this->parent_sert_series,
                'sert_num' => $this->parent_sert_num,
                'sert_organ' => $this->parent_sert_organ,
                'sert_code' => $this->parent_sert_code,
                'sert_date' => $this->parent_sert_date,
            ]);
        }
        $modelDependence->relation_id = $this->relation_id;

        $transaction = \Yii::$app->db->beginTransaction();
        $flag = false;
        try {
            $userStudent->username = $userCommonStudent->generateUsername();
            $userStudent->email = $userCommonStudent->email;
            $userStudent->generateAuthKey();
            if (!$parentsAvailable) {
                $userParent->username = $userParent->username ?? $userCommonParent->generateUsername();
                $userParent->email = $userCommonParent->email;
                $userParent->generateAuthKey();
            }

            if (Yii::$app->art->emailConfirmationRequired) {
                $userStudent->status = User::STATUS_INACTIVE;
                $userStudent->generateConfirmationToken();

                if (!$parentsAvailable) {
                    $userParent->status = User::STATUS_INACTIVE;
                    $userParent->generateConfirmationToken();
                }
            }
            if (!$parentsAvailable) {
                $userParent->save(false);
            }
            if ($flag = $userStudent->save(false)) {
                $userStudent->assignRoles(['student']);
                $userCommonStudent->user_category = UserCommon::USER_CATEGORY_STUDENTS;
                $userCommonStudent->user_id = $userStudent->id;
                if (!$parentsAvailable) {
                    $userParent->assignRoles(['parents']);
                    $userCommonParent->user_category = UserCommon::USER_CATEGORY_PARENTS;
                    $userCommonParent->user_id = $userParent->id;
                    $userCommonParent->save(false);
                }
                if ($flag = $userCommonStudent->save(false)) {
                    $modelStudent->user_common_id = $userCommonStudent->id;
                    $modelParent->user_common_id = $userCommonParent->id;
                    if ($modelStudent->save(false) && $modelParent->save(false)) {
                        $modelDependence->student_id = $modelStudent->id;
                        $modelDependence->parent_id = $modelParent->id;
                        $flag = $modelDependence->save(false);
                    }
                }
                if ($flag && Yii::$app->art->emailConfirmationRequired) {
                    $userStudent->email ? $this->sendConfirmationEmail($userStudent) : null;
                    $userParent->email && !$parentsAvailable ? $this->sendConfirmationEmail($userParent) : null;
                }
            }

            if ($flag) {
                $transaction->commit();
                return $modelStudent->id;
            }
        } catch (\Exception $e) {
            echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
            $transaction->rollBack();
            return false;
        }

    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function sendConfirmationEmail($user)
    {
        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['signup-confirmation'], ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'E-mail confirmation for') . ' ' . Yii::$app->name)
            ->send();
    }

    protected function findParentByFio()
    {
        $birth_date = Yii::$app->formatter->asTimestamp($this->parent_birth_date);

        $user = UserCommon::find()
            ->where(['like', 'last_name', $this->parent_last_name])
            ->andWhere(['like', 'first_name', $this->parent_first_name]);

        if ($this->parent_middle_name != null) {
            $user = $user->andWhere(['like', 'middle_name', $this->parent_middle_name]);
        }
        $user = $user->andWhere(['=', 'birth_date', $birth_date]);

        return $user->one() ?? new UserCommon();
    }
}