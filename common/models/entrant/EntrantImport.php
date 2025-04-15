<?php

namespace common\models\entrant;

use artsoft\models\User;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use common\models\students\Student;
use common\models\subject\Subject;
use common\models\user\UserCommon;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class EntrantImport extends Model
{
    public $file;
    public $com_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
//            ['file', 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx'],
            [['com_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Файл для импорта с портала Mos.ru или Мосбилета',
        ];
    }

    public function upload()
    {
        $data = [];
        $fileName = Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $this->file->baseName . '.' . $this->file->extension;
        if ($this->validate()) {
            $this->file->saveAs($fileName);
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($fileName);
            foreach ($reader->getSheetIterator() as $k => $sheet) {
                if (1 != $k) {
                    continue;
                }
                foreach ($sheet->getRowIterator() as $i => $row) {
                    /* @var $row Row */
                    $v = $row->toArray();
                    if ($v[1] == 'Mos.ru') {
                        $data[] = [
                            'fullname' => $v[3],
                            'snils' => $v[4],
                            'subjects' => $v[5],
                        ];
                    } elseif ($v[1] == 'МПГУ') {
                        $data[] = [
                            'fullname' => $v[4],
                            'snils' => $v[5],
                            'subjects' => $v[3],
                            'birth_date' => $v[6],
                            'gender' => $v[7],
                        ];
                    } else {
                        if (1 == $i) {
                            continue;
                        }
                        $data[] = [
                            'fullname' => $v[3],
                        ];
                    }
                }
            }
            $data_array = ArrayHelper::index($data, null,'fullname');
            $data = ArrayHelper::index($data, 'fullname');
           // echo '<pre>' . print_r($data, true) . '</pre>'; die();
            foreach (array_unique($data, SORT_REGULAR) as $fullName => $val) {
                $student_id = self::findByFio($fullName);
                if (!$student_id) {
                    $array = explode(' ', $fullName);
                    $user = new User();
                    $userCommon = new UserCommon();
                    $modelStudent = new Student();
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $userCommon->last_name = $array[0];
                        $userCommon->first_name = $array[1];
                        $userCommon->middle_name = $array[2];
                        $userCommon->birth_date = $val['birth_date'] ?? null;
                        $user->username = $userCommon->generateUsername();
                        $user->generateAuthKey();
                        $user->status = User::STATUS_INACTIVE;

                        if ($flag = $user->save(false)) {
                            $user->assignRoles(['student']);

                            $userCommon->user_id = $user->id;
                            $userCommon->user_category = UserCommon::USER_CATEGORY_STUDENTS;
                            $userCommon->status = UserCommon::STATUS_ACTIVE;
                            $userCommon->snils = $val['snils'];
                            $userCommon->gender = $this->getGender($val['gender']);
                           // echo '<pre>' . print_r($data, true) . '</pre>';
                            //echo '<pre>' . print_r($this->getSubjects($data_array[$fullName]), true) . '</pre>';
                            if ($flag = $userCommon->save(false)) {
                                $modelStudent->user_common_id = $userCommon->id;
                                if (!($flag = $modelStudent->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $student_id = $modelStudent->id;
                            $transaction->commit();
                        }
                    } catch (\Exception $e) {
                        // echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
                        $transaction->rollBack();
                    }
                }
                if ($student_id) {
                    $model = Entrant::find()->where(['comm_id' => $this->com_id])->andWhere(['student_id' => $student_id])->one();
                    if (!$model) {
                        $model = new Entrant();
                        $model->student_id = $student_id;
                        $model->comm_id = $this->com_id;
                        $model->status = 0;
                        $model->group_id = $this->getGroup($this->com_id);
                        $model->subject_list = $this->getSubjects($data_array[$fullName]);
                        $model->save(false);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    protected static function findByFio($fullName)
    {
        $array = explode(' ', $fullName);
        $user = (new Query())->from('students_view')
            ->select(['students_id'])
            ->where(['AND',
                ['first_name' => $array[1]],
                ['last_name' => $array[0]],
            ]);

        if ($array[2] != null) {
            $user = $user->andWhere(['like', 'middle_name', $array[2]]);
        }

        return $user->scalar();
    }

    protected function getSubjects($array)
    {
        $subject = [];
        $array = ArrayHelper::getColumn($array, 'subjects');
        foreach ($array as $name) {
            $subject[] = $this->getSubject($name);
        }
        return $subject;
    }

    protected function getSubject($name)
    {
        $subject = Subject::findOne(['name' => $this->getSubjectName($name)]);
        return $subject->id ?? null;
    }

    protected function getGender($name)
    {
        return $name == 'Мужской' ? UserCommon::GENDER_MALE : ($name == 'Женский' ? UserCommon::GENDER_FEMALE : UserCommon::GENDER_NOT_SET);
    }

    protected function getSubjectName($name)
    {
        $array = explode(',', $name);
        $name =  $array[0];
        switch ($name) {
            case 'Музыкальный фольклор' :
                $name = 'Фольклорный ансамбль';
                break;
            case 'Хоровое пение' :
                $name = 'Хор';
                break;
            case 'Ударные инструменты' :
                $name = 'Ударные';
                break;
        }

        return $name;
    }

    protected function getGroup($comm_id)
    {
        $model = EntrantGroup::find()->where(['comm_id' => $comm_id])->andWhere(['name' => 'Mos.ru'])->one() ?? new EntrantGroup();
        $model->name = 'Mos.ru';
        $model->timestamp_in = \Yii::$app->formatter->asDatetime(time()-10800, 'php:d.m.Y H:i');
        $model->comm_id = $comm_id;
        $model->prep_flag = 0;

        return $model->save(false) ? $model->id : false;
    }
}