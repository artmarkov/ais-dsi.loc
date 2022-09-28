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
 * @property int $entrant_members_id
 * @property int $entrant_test_id
 * @property int|null $entrant_mark_id
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideEntrantTest $entrantTest
 * @property EntrantMembers $entrantMembers
 * @property GuideLessonMark $entrantMark
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
            [['entrant_members_id', 'entrant_test_id'], 'required'],
            [['entrant_members_id', 'entrant_test_id', 'entrant_mark_id', 'version'], 'integer'],
            [['entrant_members_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantMembers::className(), 'targetAttribute' => ['entrant_members_id' => 'id']],
            [['entrant_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideEntrantTest::className(), 'targetAttribute' => ['entrant_test_id' => 'id']],
            [['entrant_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['entrant_mark_id' => 'id']],
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'entrant_members_id' => Yii::t('art/guide', 'Members Item'),
            'entrant_test_id' => Yii::t('art/guide', 'Entrant Test Item'),
            'entrant_mark_id' => Yii::t('art/guide', 'Entrant Mark'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }


    /**
     * Gets query for [[EntrantMembers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantMembers()
    {
        return $this->hasOne(EntrantMembers::className(), ['id' => 'entrant_members_id']);
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
