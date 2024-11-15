<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\models\User;
use common\models\teachers\TeachersLoadStudyplanView;
use Yii;

/**
 * @property string|null $education_programm_name
 * @property string|null $education_programm_short_name
 * @property string|null $education_cat_name
 * @property string|null $education_cat_short_name
 * @property string|null $student_fio
 * @property string|null $subject_form_name
 * @property string|null $speciality
 * @property string|null $user_phone
 * @property string|null $education_cat_id
 */
class StudyplanView extends Studyplan
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_view';
    }

    public function attributeLabels()
    {

        $attr = parent::attributeLabels();

        $attr['education_programm_name'] = 'Программа';
        $attr['education_programm_short_name'] = 'Программа';
        $attr['education_cat_name'] = 'Категория программы';
        $attr['education_cat_short_name'] = 'Категория программы';
        $attr['education_cat_id'] = 'Категория программы';
        $attr['student_fio'] = 'Ученик';
        $attr['subject_form_name'] = 'Тип занятий';
        $attr['speciality'] = 'Специальность';
        $attr['user_phone'] = 'Телефон';

        return $attr;
    }


    public static function getStudyplanList($id)
    {
        $model = self::find()->where(['=', 'id', $id])->one();
        if (!$model) return [];
        $query = [];

        if (User::hasRole(['parents'])) {
            $userId = Yii::$app->user->identity->getId();
            $parents_id = \artsoft\helpers\RefBook::find('users_parents')->getValue($userId) ?? null;
            $ids = Studyplan::find()
                ->innerJoin('student_dependence', 'studyplan.student_id=student_dependence.student_id')
                ->where(['=', 'student_dependence.parent_id', $parents_id])
                ->andWhere(['=', 'plan_year', $model->plan_year])
                ->active()->column();

            $query = self::find()
                ->select(['studyplan_view.id', "CONCAT(student_fio, ' - ',education_programm_short_name) as name"])
                ->where(['id' => $ids])
                ->asArray()->all();

        } elseif (User::hasRole(['student'])) {

            $query = self::find()->select('id,education_programm_short_name as name')
                ->where(['=', 'student_id', $model->student_id])
                ->andWhere(['=', 'plan_year', $model->plan_year])
                ->active()
                ->asArray()->all();
        }
        return \yii\helpers\ArrayHelper::map($query, 'id', 'name');
    }

    public static function getStudyplanListByPlanYear($plan_year)
    {
            $query = self::find()->select(['id', 'CONCAT(student_fio, \' - \', education_programm_short_name) as name'])
                ->where(['=', 'plan_year', $plan_year])
                ->active()
                ->asArray()->all();

        return \yii\helpers\ArrayHelper::map($query, 'id', 'name');
    }

}
