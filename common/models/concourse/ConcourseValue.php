<?php

namespace common\models\concourse;

use artsoft\models\User;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "concourse_value".
 *
 * @property int $id
 * @property int $users_id
 * @property int $concourse_item_id
 * @property int $concourse_criteria_id
 * @property int|null $concourse_mark
 * @property string|null $concourse_string
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property ConcourseItem $concourseItem
 * @property ConcourseItem $concourseCriteria
 * @property Users $users
 */
class ConcourseValue extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concourse_value';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['users_id', 'concourse_item_id', 'concourse_criteria_id', 'concourse_mark'], 'required'],
            [['users_id', 'concourse_item_id', 'concourse_criteria_id', 'concourse_mark', 'version'], 'default', 'value' => null],
            [['users_id', 'concourse_item_id', 'concourse_criteria_id', 'concourse_mark', 'version'], 'integer'],
            [['concourse_string'], 'string', 'max' => 1024],
            [['users_id', 'concourse_item_id', 'concourse_criteria_id'], 'unique', 'on' => $this->isNewRecord],
            [['concourse_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConcourseItem::className(), 'targetAttribute' => ['concourse_item_id' => 'id']],
            [['users_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['users_id' => 'id']],
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
            'users_id' => 'Users ID',
            'concourse_item_id' => 'Concourse Item ID',
            'concourse_criteria_id' => 'Concourse Criteria ID',
            'concourse_mark' => 'Concourse Mark',
            'concourse_string' => 'Concourse String',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'version' => 'Version',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[ConcourseItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourseItem()
    {
        return $this->hasOne(ConcourseItem::className(), ['id' => 'concourse_item_id']);
    }

    /**
     * Gets query for [[ConcourseCriteria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourseCriteria()
    {
        return $this->hasOne(ConcourseCriteria::className(), ['id' => 'concourse_criteria_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'users_id']);
    }

}
