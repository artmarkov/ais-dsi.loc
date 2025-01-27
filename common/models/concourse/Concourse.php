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
 * @property int $vid_id Выбор участников
 * @property bool $authors_ban_flag Запрет на оценку своих работ
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
    const VID_AUTHORS = 1;
    const VID_USERS = 2;

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
            [['name', 'timestamp_in', 'timestamp_out'], 'required'],
            [['author_id', 'status'], 'default', 'value' => null],
            [['vid_id'], 'default', 'value' => 1],
            [['author_id', 'status', 'vid_id'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['authors_ban_flag'], 'boolean'],
            [['timestamp_in', 'timestamp_out'], 'safe'],
            [['users_list'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['users_list'], 'required', 'when' => function ($model) {
                return $model->vid_id === '2';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[name=\"Concourse[vid_id]\"]:checked').val() === '2';
                            }"],
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
            'vid_id' => 'Выбор участников',
            'users_list' => 'Список участников',
            'description' => 'Описание конкурса',
            'timestamp_in' => 'Начало действия',
            'timestamp_out' => 'Окончание действия',
            'authors_ban_flag' => 'Запрет на оценку своих работ',
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

    public static function getVidList()
    {
        return array(
            self::VID_AUTHORS => 'Авторы работ',
            self::VID_USERS => 'Список участников',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getVidValue($val)
    {
        $ar = self::getVidList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->vid_id == self::VID_AUTHORS) {
            $this->users_list = null;
        }
        return parent::beforeSave($insert);
    }
}
