<?php

namespace common\models\entrant;

use common\models\own\Division;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "guide_entrant_test".
 *
 * @property int $id
 * @property int $division_id
 * @property string|null $name Название испытания
 * @property string|null $name_dev Сокращенное название испытания
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 *
 * @property EntrantTest[] $entrantTests
 * @property GuideDivision $division
 */
class GuideEntrantTest extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_entrant_test';
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
            [['division_id', 'name', 'name_dev'], 'required'],
            [['status', 'division_id'], 'integer'],
            [['name', 'name_dev'], 'string', 'max' => 255],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_id' => Yii::t('art/guide', 'Division'),
            'name' => Yii::t('art', 'Name'),
            'name_dev' => Yii::t('art', 'Short Name'),
            'status' => Yii::t('art', 'Status'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[EntrantTests]].
     *
     * @return \yii\db\ActiveQuery
     */
//    public function getEntrantTests()
//    {
//        return $this->hasMany(EntrantTest::className(), ['entrant_test_id' => 'id']);
//    }

    /**
     * Gets query for [[Division]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }
}
