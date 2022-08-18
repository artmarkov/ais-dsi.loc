<?php

namespace common\models\entrant;

use common\models\education\LessonMark;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "entrant_test".
 *
 * @property int $id
 * @property int $entrant_id
 * @property int $members_id Член комиссии
 * @property int $entrant_test_id
 * @property int|null $entrant_mark_id
 * @property string|null $mark_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Entrant $entrant
 * @property GuideEntrantTest $entrantTest
 * @property GuideLessonMark $entrantMark
 * @property Users $createdBy0
 * @property Users $updatedBy0
 */
class EntrantTest extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_test';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entrant_id', 'members_id', 'entrant_test_id', 'created_at', 'updated_at'], 'required'],
            [['entrant_id', 'members_id', 'entrant_test_id', 'entrant_mark_id', 'version'], 'integer'],
            [['mark_rem'], 'string', 'max' => 127],
            [['entrant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entrant::className(), 'targetAttribute' => ['entrant_id' => 'id']],
            [['entrant_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideEntrantTest::className(), 'targetAttribute' => ['entrant_test_id' => 'id']],
            [['entrant_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['entrant_mark_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'entrant_id' => Yii::t('art/guide', 'Entrant'),
            'members_id' => Yii::t('art/guide', 'Members'),
            'entrant_test_id' => Yii::t('art/guide', 'Entrant Test'),
            'entrant_mark_id' => Yii::t('art/guide', 'Entrant Mark'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    /**
     * Gets query for [[Entrant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrant()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'entrant_id']);
    }

    /**
     * Gets query for [[EntrantTest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantTest()
    {
        return $this->hasOne(GuideEntrantTest::className(), ['id' => 'entrant_test_id']);
    }

    /**
     * Gets query for [[EntrantMark]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantMark()
    {
        return $this->hasOne(LessonMark::className(), ['id' => 'entrant_mark_id']);
    }

}
