<?php

namespace common\models\entrant;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ExcelObjectList;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\own\Division;
use common\models\subject\SubjectForm;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "entrant_comm".
 *
 * @property int $id
 * @property int $division_id
 * @property string $department_list
 * @property int $plan_year Учебный год
 * @property string|null $name Название комиссии
 * @property int $leader_id Реководитель комиссии user_id
 * @property int $secretary_id Секретарь комиссии user_id
 * @property string|null $members_list Члены комиссии user_id
 * @property string|null $prep_on_test_list Список испытаний с подготовкой
 * @property string|null $prep_off_test_list Список испытаний без подготовки
 * @property int $timestamp_in Начало действия
 * @property int $timestamp_out Окончание действия
 * @property string|null $description План работы комиссии
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Entrant[] $entrants
 * @property GuideDivision $division
 * @property Users $leader
 * @property Users $secretary
 * @property EntrantGroup[] $entrantGroups
 */
class EntrantComm extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_comm';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['members_list', 'prep_on_test_list', 'prep_off_test_list', 'department_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['timestamp_in', 'timestamp_out'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'division_id', 'plan_year', 'leader_id', 'secretary_id', 'timestamp_in', 'timestamp_out', 'members_list', 'prep_on_test_list', 'prep_off_test_list', 'department_list'], 'required'],
            [['division_id', 'plan_year', 'leader_id', 'secretary_id', 'version'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['members_list', 'prep_on_test_list', 'prep_off_test_list', 'timestamp_in', 'timestamp_out', 'department_list'], 'safe'],
            [['description'], 'string', 'max' => 1024],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['leader_id' => 'id']],
            [['secretary_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['secretary_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'division_id' => Yii::t('art/guide', 'Division'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'name' => Yii::t('art', 'Name'),
            'leader_id' => Yii::t('art/guide', 'Leader'),
            'secretary_id' => Yii::t('art/guide', 'Secretary'),
            'members_list' => Yii::t('art/guide', 'Members List'),
            'prep_on_test_list' => Yii::t('art/guide', 'Prep On Test List'),
            'prep_off_test_list' => Yii::t('art/guide', 'Prep Off Test List'),
            'timestamp_in' => Yii::t('art/guide', 'Timestamp In'),
            'timestamp_out' => Yii::t('art/guide', 'Timestamp Out'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Entrants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrants()
    {
        return $this->hasMany(Entrant::className(), ['comm_id' => 'id']);
    }

    /**
     * Gets query for [[Division]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }

    /**
     * Gets query for [[Leader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'leader_id']);
    }

    /**
     * Gets query for [[Secretary]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecretary()
    {
        return $this->hasOne(User::className(), ['id' => 'secretary_id']);
    }


    /**
     * Gets query for [[EntrantGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantGroups()
    {
        return $this->hasMany(EntrantGroup::className(), ['comm_id' => 'id']);
    }

    public static function getComList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'name');

    }

    /**
     * @return array
     */
    public function getEntrantGroupsList()
    {
        return ArrayHelper::map($this->entrantGroups, 'id', 'name');
    }

    /**
     * Список членов комиссии
     * @return array
     */
    public function getEntrantMembersList()
    {
        return \yii\helpers\ArrayHelper::map(UserCommon::find()
            ->innerJoin('users', "user_common.user_id = users.id")
            ->where(['in', 'users.id', $this->members_list])
            ->select(['users.id as id', "CONCAT(user_common.last_name, ' ',user_common.first_name, ' ',user_common.middle_name) AS name"])
            ->orderBy('user_common.last_name')
            ->asArray()->all(), 'id', 'name');
    }

    /**
     * @param $id
     * @return GuideEntrantTest[]
     * @throws NotFoundHttpException
     */
    public function getTests($id)
    {
        $model = EntrantGroup::findOne($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The EntrantGroup was not found.");
        }
        $ids = $model->prep_flag == 1 ? $this->prep_on_test_list : $this->prep_off_test_list;
        return GuideEntrantTest::findAll(['id' => $ids]);
    }

    /**
     * @param $model_date
     * @return array
     */
    public function getSummaryData($model_date)
    {
        $members_id = $model_date->members_id;
        $free_flag = $model_date->free_flag;
        $prep_flag = $model_date->prep_flag;
        $prep_list = $prep_flag == 0 ? $this->prep_off_test_list : $this->prep_on_test_list;

        $testsNames = \yii\helpers\ArrayHelper::map(GuideEntrantTest::find()
            ->where(['in', 'id', $prep_list])
            ->select(['id', 'name'])
            ->orderBy('name')
            ->asArray()->all(), 'id', 'name');

        $models = (new Query())->from('entrant_members_view')
            ->where(['in', 'members_id', $members_id != 0 ? $members_id : $this->members_list])
            ->andWhere(['=', 'prep_flag', $prep_flag])
            ->orderBy('mid_mark DESC')->all();
        $models = ArrayHelper::index($models, null, ['student_id', 'entrant_test_id']);
        $modelsEntrant = (new Query())->from('entrant_view')
            ->where(['=', 'comm_id', $this->id])
            ->andWhere(['=', 'prep_flag', $prep_flag])
            ->orderBy('mid_mark DESC')->distinct()->all();

        $attributes = ['name' => 'Фамилия И.О.'];
        $attributes += ['birth_date' => 'Дата рождения (возраст)'];
        $attributes += ['group' => 'Группа'];
        $attributes += $testsNames;
        $attributes += ['mid_mark' => 'Средняя оценка'];
        $attributes += ['decision' => 'Решение комиссии'];
        $attributes += ['programm' => 'Назначен учебный план'];
        $attributes += ['subject' => 'Специальность'];
        $attributes += ['course' => 'Назначен клвсс'];
        $attributes += ['subject_form' => 'Форма обучения'];

        $data = [];
        foreach ($modelsEntrant as $id => $model) {
            $mid_mark = [];
            $age = \artsoft\helpers\ArtHelper::age($model['birth_date']);
            $data[$id]['name'] = $model['fullname'];
            $data[$id]['birth_date'] = Yii::$app->formatter->asDate($model['birth_date']) . ' (' . $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.)';
            $data[$id]['group'] = $model['group_name'];
            $data[$id]['decision'] = !$free_flag ? Entrant::getDecisionValue($model['decision_id']) : null;
            $data[$id]['programm'] = !$free_flag ? RefBook::find('education_programm_name')->getValue($model['programm_id']) : null;
            $data[$id]['subject'] = !$free_flag ? RefBook::find('subject_name')->getValue($model['subject_id']) : null;
            $data[$id]['course'] = !$free_flag ? $model['course'] : null;
            $data[$id]['subject_form'] = !$free_flag ? SubjectForm::getFormValue($model['subject_form_id']) : null;
            foreach ($testsNames as $ids => $name) {
                if (isset($models[$model['student_id']][$ids])) {
                    $mark = [];
                    foreach ($models[$model['student_id']][$ids] as $item => $value) {
                        $mark[] = $value['mark_value'];
                    }
                    $data[$id][$ids] = (!empty($mark) && !$free_flag) ? round((array_sum($mark) / count($mark)), 2) : '';
                    $mid_mark[] = $data[$id][$ids];
                }
            }
            $data[$id]['mid_mark'] = (!empty($mid_mark) && !$free_flag) ? round((array_sum($mid_mark) / count($mid_mark)), 2) : '';
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });

        return ['data' => $data, 'attributes' => $attributes];
    }

    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }
//            $x->addData(['stake' => 'Итого', 'total' => $data['all_summ']]);

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_entrant_result.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }
}
