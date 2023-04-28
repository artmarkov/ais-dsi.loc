<?php

namespace common\models\forms;

use Yii;
use yii\base\Model;

class RegistrationForm extends Model
{
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

    public $email;
    public $phone;
    public $phone_optional;

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


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'phone'], 'required'],
            [['student_first_name', 'student_last_name', 'student_birth_date'], 'required'],
            [['parent_first_name',  'parent_last_name', 'parent_birth_date'], 'required'],
            [['relation_id'], 'required'],
            [['student_snils', 'parent_snils'], 'required'],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'trim'],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'trim'],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'string', 'max' => 124],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'string', 'max' => 124],
            [['student_first_name', 'student_middle_name', 'student_last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['parent_first_name', 'parent_middle_name', 'parent_last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            [['student_birth_date'],'safe'],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            [['student_snils', 'parent_snils'], 'string', 'max' => 16],
            ['email', 'email'],
            [['student_sert_date'], 'date'],
            [['student_sert_name', 'student_sert_series', 'student_sert_num'], 'string', 'max' => 32],
            [['student_sert_organ'], 'string', 'max' => 127],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['student_sert_series', 'student_sert_num', 'student_sert_organ', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_name != NULL;
            }, 'enableClientValidation' => true],
            [['student_sert_name', 'student_sert_num', 'student_sert_organ', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_series != NULL;
            }, 'enableClientValidation' => true],
            [['student_sert_name', 'student_sert_series', 'student_sert_organ', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_num != NULL;
            }, 'enableClientValidation' => true],
            [['student_sert_name', 'student_sert_num', 'student_sert_series', 'student_sert_date'], 'required', 'when' => function ($model) {
                return $model->student_sert_organ != NULL;
            }, 'enableClientValidation' => true],
            [['student_sert_name', 'student_sert_num', 'student_sert_series', 'student_sert_organ'], 'required', 'when' => function ($model) {
                return $model->student_sert_date != NULL;
            }, 'enableClientValidation' => true],
            ['student_sert_date', 'default', 'value' => NULL],

            [['parent_sert_date'], 'date'],
            [['parent_sert_name', 'parent_sert_series', 'parent_sert_num', 'parent_sert_code'], 'string', 'max' => 32],
            [['parent_sert_organ'], 'string', 'max' => 127],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['parent_sert_series', 'parent_sert_num', 'parent_sert_organ', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_name != NULL;
            }, 'enableClientValidation' => true],
            [['parent_sert_name', 'parent_sert_num', 'parent_sert_organ', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_series != NULL;
            }, 'enableClientValidation' => true],
            [['parent_sert_name', 'parent_sert_series', 'parent_sert_organ', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_num != NULL;
            }, 'enableClientValidation' => true],
            [['parent_sert_name', 'parent_sert_num', 'parent_sert_series', 'parent_sert_date'], 'required', 'when' => function ($model) {
                return $model->parent_sert_organ != NULL;
            }, 'enableClientValidation' => true],
            [['parent_sert_name', 'parent_sert_num', 'parent_sert_series', 'parent_sert_organ'], 'required', 'when' => function ($model) {
                return $model->parent_sert_date != NULL;
            }, 'enableClientValidation' => true],
            ['parent_sert_date', 'default', 'value' => NULL],
            [['student_gender', 'parent_gender'], 'integer'],


        ];
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
            'student_gender' => Yii::t('art', 'Gender'),
            'parent_gender' => Yii::t('art', 'Gender'),
        ];
    }

}