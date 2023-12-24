<?php

namespace common\models\studyplan;

use common\models\education\PieceCategory;
use Yii;

/**
 * This is the model class for table "studyplan_thematic_items".
 *
 * @property int $id
 * @property int|null $studyplan_thematic_id
 * @property string $task
 * @property string|null $topic
 *
 * @property StudyplanThematic $studyplanThematic
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
            [['topic'], 'required'],
            [['studyplan_thematic_id'], 'integer'],
            [['task', 'topic'], 'string', 'max' => 1024],
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
            'task' => Yii::t('art/studyplan', 'Task'),
            'topic' => Yii::t('art/studyplan', 'Topic'),
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
