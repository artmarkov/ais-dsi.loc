<?php

namespace common\models\planfix;

use artsoft\db\ActiveRecord;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;

/**
 * This is the model class for table "planfix".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name Название задания
 * @property string|null $description Описание задания
 * @property int $planfix_author Автор задания
 * @property string|null $executors_list Исполнители задания
 * @property int $importance Приоритет работы(высокий, обычный, низкий)
 * @property int $planfix_date Планируемая дата выполнения
 * @property int $status Статус работы(В работе, Выполнено, Не выполнено)
 * @property string|null $status_reason Причина невыполнения
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property PlanfixCategory $category
 * @property User $createdBy
 * @property User $updatedBy
 * @property PlanfixActivity[] $planfixActivities
 */
class Planfix extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'planfix';
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
                'attributes' => ['executors_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['planfix_date'],
                'timeFormat' => 'd.m.Y'
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
            [['category_id', 'name', 'planfix_author', 'executors_list', 'importance', 'planfix_date', 'status'], 'required'],
            [['category_id', 'planfix_author', 'importance'], 'default', 'value' => null],
            [['category_id', 'planfix_author', 'importance', 'status', 'created_at', 'updated_at', 'version'], 'integer'],
            [['status_reason', 'description'], 'string', 'max' => 1024],
            [['name'], 'string', 'max' => 512],
            [['executors_list', 'planfix_date'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PlanfixCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => 'Категория',
            'name' => 'Название задания',
            'description' => 'Описание задания',
            'planfix_author' => 'Автор задания',
            'executors_list' => 'Исполнители задания',
            'importance' => 'Приоритет работы',
            'planfix_date' => 'Планируемая дата',
            'status_reason' => 'Причина невыполнения',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => 'Статус работы',
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(PlanfixCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->category->name;
    }

    /**
     * Gets query for [[PlanfixActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlanfixActivities()
    {
        return $this->hasMany(PlanfixActivity::className(), ['planfix_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            1 => 'В работе',
            2 => 'Выполнено',
            3 => 'Не выполнено',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusOptionsList()
    {
        return [
            [1, 'В работе', 'info'],
            [2, 'Выполнено', 'success'],
            [3, 'Не выполнено', 'danger']
        ];
    }

    public static function getImportanceList()
    {
        return [
            1 => 'Высокий',
            2 => 'Обычный',
            3 => 'Низкий',
        ];
    }

    /**
     * @return array
     */
    public static function getImportanceOptionsList()
    {
        return [
            [1, 'Высокий', 'danger'],
            [2, 'Обычный', 'info'],
            [3, 'Низкий', 'default'],
        ];
    }

    public static function getImportanceValue($val)
    {
        $ar = self::getImportanceList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public function sendPlanfixMessage($content)
    {
            $title = 'Сообщение модуля "Планировщик задач"';
            return Yii::$app->mailbox->mailing($this->executors_list, $content, $title);
    }
}
