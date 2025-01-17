<?php

namespace common\models\concourse;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "concourse".
 *
 * @property int $id
 * @property int $author_id
 * @property string $name Название конкурса
 * @property string|null $users_list Список участников
 * @property string|null $description Описание конкурса
 * @property int $timestamp_in Начало действия
 * @property int $timestamp_out Окончание действия
 * @property int $status Статус формы (Активная, Не активная)
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Users $author
 * @property Users $createdBy
 * @property Users $updatedBy
 * @property ConcourseCriteria[] $concourseCriterias
 * @property ConcourseItem[] $concourseItems
 */
class Concourse extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concourse';
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
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['users_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['timestamp_in', 'timestamp_out'],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'name', 'timestamp_in', 'timestamp_out', 'users_list'], 'required'],
            [['author_id', 'status'], 'default', 'value' => null],
            [['author_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['timestamp_in', 'timestamp_out'], 'safe'],
            [['users_list'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор конкурса',
            'name' => 'Название конкурса',
            'users_list' => 'Список участников',
            'description' => 'Описание конкурса',
            'timestamp_in' => 'Начало действия',
            'timestamp_out' => 'Окончание действия',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[CreatedBy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ConcourseCriterias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourseCriterias()
    {
        return $this->hasMany(ConcourseCriteria::className(), ['concourse_id' => 'id']);
    }

    /**
     * Gets query for [[ConcourseItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourseItems()
    {
        return $this->hasMany(ConcourseItem::className(), ['concourse_id' => 'id']);
    }
}
