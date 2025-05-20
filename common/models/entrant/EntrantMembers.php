<?php

namespace common\models\entrant;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "entrant_members".
 *
 * @property int $id
 * @property int $entrant_id
 * @property int $members_id Член комиссии
 * @property string|null $mark_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Entrant $entrant
 * @property EntrantTest $entrantTest
 */
class EntrantMembers extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_members';
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
            [['entrant_id', 'members_id'], 'required'],
            [['mark_rem'], 'string', 'max' => 1024],
            [['entrant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entrant::className(), 'targetAttribute' => ['entrant_id' => 'id']],
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
            'entrant_id' => Yii::t('art/guide', 'Entrant'),
            'members_id' => Yii::t('art/guide', 'Members Item'),
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
        return $this->hasMany(EntrantTest::className(), ['entrant_members_id' => 'id']);
    }

    /**
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getEntrantTestDefault()
    {
        $models = [];
        $group_id = $this->entrant->group->id;
        $modelsGuideTest = $this->entrant->group->comm->getTests($group_id);

        foreach ($modelsGuideTest as $item => $modelGuideTest){
            $model = EntrantTest::find()->andWhere(['=', 'entrant_members_id', $this->id])->andWhere(['=', 'entrant_test_id', $modelGuideTest->id])->one() ?: new EntrantTest();
            $model->entrant_members_id = $this->id;
            $model->entrant_test_id = $modelGuideTest->id;
            $models[] = $model;
        }
        return $models;
    }
}
