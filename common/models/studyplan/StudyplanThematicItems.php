<?php

namespace common\models\studyplan;

use common\models\education\PieceCategory;
use Yii;

/**
 * This is the model class for table "studyplan_thematic_items".
 *
 * @property int $id
 * @property int|null $studyplan_thematic_id
 * @property string $author
 * @property string $piece_name
 * @property int $piece_category_id
 * @property string|null $task
 *
 * @property StudyplanThematic $studyplanThematic
 * @property PieceCategory $pieceCategory
 */
class StudyplanThematicItems extends \artsoft\db\ActiveRecord
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
            [['studyplan_thematic_id', 'piece_category_id'], 'integer'],
            [['task'], 'string', 'max' => 1024],
            [['author', 'piece_name', 'piece_category_id'], 'required', 'when' => function () { return $this->studyplanThematic ? $this->studyplanThematic->thematic_category == StudyplanThematic::REPERTORY_PLAN : false; } ],
            [['author', 'piece_name'], 'string', 'max' => 256],
            [['studyplan_thematic_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanThematic::className(), 'targetAttribute' => ['studyplan_thematic_id' => 'id']],
            [['piece_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PieceCategory::className(), 'targetAttribute' => ['piece_category_id' => 'id']],
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
            'author' => Yii::t('art/studyplan', 'Piece Author'),
            'piece_name' => Yii::t('art/studyplan', 'Piece Name'),
            'piece_category_id' => Yii::t('art/studyplan', 'Piece Category'),
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

    /**
     * Gets query for [[PieceCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPieceCategory()
    {
        return $this->hasOne(PieceCategory::className(), ['id' => 'piece_category_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->studyplanThematic->thematic_category == StudyplanThematic::THEMATIC_PLAN) {
            $this->author = null;
            $this->piece_name = null;
            $this->piece_category_id = null;
        }
        return parent::beforeSave($insert);
    }
}
