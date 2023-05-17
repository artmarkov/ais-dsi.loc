<?php

namespace common\models\entrant;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use common\models\own\Division;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
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

}
