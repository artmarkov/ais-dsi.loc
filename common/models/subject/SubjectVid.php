<?php

namespace common\models\subject;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_subject_vid".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $qty_min
 * @property int $qty_max
 * @property string $info
 * @property int $status
 */
class SubjectVid extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_subject_vid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'qty_min', 'qty_max', 'status'], 'required'],
            [['qty_min', 'qty_max', 'status'], 'integer'],
            [['info'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['slug'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'qty_min' => Yii::t('art/guide', 'Qty Min'),
            'qty_max' => Yii::t('art/guide', 'Qty Max'),
            'info' => Yii::t('art/guide', 'Info'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }

    /**
     * @return array
     */
    public static function getVidList()
    {
        return ArrayHelper::map(self::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->select('id, name')
            ->orderBy('id')
            ->asArray()->all(), 'id', 'name');
    }
}

