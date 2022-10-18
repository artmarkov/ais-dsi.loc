<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schoolplan_protocol".
 *
 * @property int $id
 * @property int|null $schoolplan_id Мероприятие
 * @property string $protocol_name Название протокола
 * @property string|null $description Описание протокола
 * @property int $protocol_date Дата протокола
 * @property int $leader_id Реководитель комиссии user_id
 * @property int $secretary_id Секретарь комиссии user_id
 * @property string $members_list Члены комиссии user_id
 * @property string $subject_list Дисциплины
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Schoolplan $schoolplan
 * @property Users $leader
 * @property Users $secretary
 * @property SchoolplanProtocolItems[] $schoolplanProtocolItems
 */
class SchoolplanProtocol extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_protocol';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['protocol_date'],
                'timeFormat' => 'd.m.Y'
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['members_list', 'subject_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['protocol_name', 'protocol_date', 'leader_id', 'secretary_id', 'members_list', 'subject_list'], 'required'],
            [['schoolplan_id', 'protocol_date', 'leader_id', 'secretary_id', 'subject_list'], 'default', 'value' => null],
            [['schoolplan_id', 'leader_id', 'secretary_id'], 'integer'],
            [['protocol_name'], 'string', 'max' => 127],
            [['description'], 'string', 'max' => 512],
            [['members_list', 'protocol_date', 'subject_list'], 'safe'],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::className(), 'targetAttribute' => ['schoolplan_id' => 'id']],
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
            'id' => 'ID',
            'schoolplan_id' => 'Мероприятие',
            'protocol_name' => 'Название протокола',
            'description' => 'Описание протокола',
            'protocol_date' => 'Дата протокола',
            'leader_id' => 'Реководитель комиссии',
            'secretary_id' => 'Секретарь комиссии',
            'members_list' => 'Члены комиссии',
            'subject_list' => 'Дисциплины',
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
     * Gets query for [[Schoolplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplan()
    {
        return $this->hasOne(Schoolplan::className(), ['id' => 'schoolplan_id']);
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
     * Gets query for [[SchoolplanProtocolItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplanProtocolItems()
    {
        return $this->hasMany(SchoolplanProtocolItems::className(), ['schoolplan_protocol_id' => 'id']);
    }

    /**
     * Получаем всех учеников для выбранных дисциплин для протокола мероприятия в учебном году
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudyplanSubjectList()
    {
        $subject_list = implode(',', $this->subject_list);
        $study_year = \artsoft\helpers\ArtHelper::getStudyYearDefault($month_dev = null, $timestamp = Yii::$app->formatter->asTimestamp($this->protocol_date))-1;
        $funcSql = <<< SQL
                    select studyplan_subject_id, memo_4
                    from studyplan_subject_view where subject_id in ({$subject_list})
                        and plan_year = {$study_year}
SQL;
        return ArrayHelper::map(Yii::$app->db->createCommand($funcSql)->queryAll(), 'studyplan_subject_id', 'memo_4');
    }

    /**
     * Нахождение всех элементов репертуарного плана для $studyplan_subject_id
     * @param $studyplan_subject_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getStudyplanThematicItemsList($studyplan_subject_id)
    {
        $funcSql = <<< SQL
                    select studyplan_thematic_items.id,
		                   concat(studyplan_thematic_items.author , ' - ', studyplan_thematic_items.piece_name, ' (', guide_piece_category.name, ')') AS piece
                    FROM studyplan_thematic_view 
                    INNER JOIN studyplan_thematic_items ON studyplan_thematic_view.studyplan_thematic_id = studyplan_thematic_items.studyplan_thematic_id 
                    INNER JOIN guide_piece_category ON guide_piece_category.id = studyplan_thematic_items.piece_category_id
                    where thematic_category = 2 and studyplan_subject_id = {$studyplan_subject_id}
                        
SQL;
        return ArrayHelper::map(Yii::$app->db->createCommand($funcSql)->queryAll(), 'id', 'piece');
    }
}
