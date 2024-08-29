<?php

namespace common\models\activities;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use common\models\user\UserCommon;
use Yii;

/**
 * This is the model class for table "activities_studyplan_view".
 * @property integer $teachers_id
 */
class ActivitiesStudyplanView extends Activities
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities_studyplan_view';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public static function getStudentList()
    {
        if (User::hasRole(['parents'],false)) {
            $userId = Yii::$app->user->identity->getId();
            $parents_id = \artsoft\helpers\RefBook::find('users_parents')->getValue($userId) ?? null;
            $ids = Student::find()->select('user_common_id')
                ->innerJoin('student_dependence', 'students.id=student_dependence.student_id')
                ->where(['=', 'student_dependence.parent_id', $parents_id])
                ->column();

            $query = UserCommon::find()
                ->select(['id', 'CONCAT(last_name, \' \',first_name, \' \',middle_name) as name'])
                ->where(['user_category' => 'students'])
                ->andWhere(['id' => $ids])
                ->andWhere(['=', 'status', UserCommon::STATUS_ACTIVE])
                ->asArray()->all();
            $data = \yii\helpers\ArrayHelper::map($query, 'id', 'name');
        } else {

            $data = RefBook::find('students_fullname', 1)->getList();
        }
        return $data;
    }

    public static function getStudentScalar()
    {
        $data = self::getStudentList();
        return array_keys($data)[0] ?? null;
    }
}
