<?php

namespace artsoft\block\models;

use artsoft\behaviors\MultilingualBehavior;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\behaviors\SluggableBehavior;
use artsoft\db\ActiveRecord;
use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "{{%block}}".
 *
 * @property integer $id
 * @property string $slug
 * @property string $title
 * @property string $content
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $sortOrder
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class Block extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'block';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'in_attribute' => 'title',
                'out_attribute' => 'slug',
                'translit' => true
            ],
            'grid-sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sortOrder',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            ['slug', 'required', 'enableClientValidation' => false],
            ['sortOrder', 'integer'],
            [['title', 'content'], 'string'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['slug'], 'string', 'max' => 200],
            [['slug'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'slug' => Yii::t('art', 'Slug'),
            'title' => Yii::t('art', 'Title'),
            'content' => Yii::t('art', 'Content'),
            'created_by' => Yii::t('art', 'Author'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
        ];
    }

    /**
     * @inheritdoc
     * @return BlockQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlockQuery(get_called_class());
    }

    /**
     * @param $slug
     * @param array $variables
     * @param null $defaultValue
     * @return mixed|null|string
     */
    public static function getHtml($slug, $variables = [], $defaultValue = null)
    {
        if ($block = self::findOne(['slug' => $slug])) {

            $content = $block->content;

            if (is_array($variables) && !empty(is_array($variables))) {
                $keys = array_map(function ($var) {
                    return '{{' . $var . '}}';
                }, array_keys($variables));

                $content = str_replace($keys, $variables, $content);
            }

            return $content;
        }

        return $defaultValue;
    }

    /**
     * @param $slug
     * @param null $defaultValue
     * @return null|string
     */
    public static function getTitle($slug, $defaultValue = null)
    {
        if ($block = self::findOne(['slug' => $slug])) {

            $title = $block->title;

            return $title;
        }

        return $defaultValue;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

}