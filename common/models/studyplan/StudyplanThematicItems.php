<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "studyplan_thematic_items".
 *
 * @property int $id
 * @property int|null $studyplan_thematic_id
 * @property string $name
 * @property string $author
 * @property string $piece_name
 * @property int $piece_category
 * @property string|null $task
 *
 * @property StudyplanThematic $studyplanThematic
 */
class StudyplanThematicItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_thematic_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studyplan_thematic_id', 'piece_category'], 'default', 'value' => null],
            [['studyplan_thematic_id', 'piece_category'], 'integer'],
            [['name', 'author', 'piece_name', 'piece_category'], 'required'],
            [['name', 'author', 'piece_name'], 'string', 'max' => 256],
            [['task'], 'string', 'max' => 1024],
            [['studyplan_thematic_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanThematic::className(), 'targetAttribute' => ['studyplan_thematic_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'studyplan_thematic_id' => Yii::t('art/studyplan', 'Studyplan Thematic'),
            'name' => Yii::t('art', 'Name'),
            'author' => Yii::t('art/studyplan', 'Piece Author'),
            'piece_name' => Yii::t('art/studyplan', 'Piece Name'),
            'piece_category' => Yii::t('art/studyplan', 'Piece Category'),
            'task' => Yii::t('art/studyplan', 'Task'),
        ];
    }

    /**
     * Gets query for [[StudyplanThematic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanThematic()
    {
        return $this->hasOne(StudyplanThematic::className(), ['id' => 'studyplan_thematic_id']);
    }
}
