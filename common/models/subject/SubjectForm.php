<?php

namespace common\models\subject;

use artsoft\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_subject_form".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $status
 */
class SubjectForm extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_subject_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 64],
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
            'type_id' => Yii::t('art/guide', 'Subject Type Name'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }


    /**
     * @return array
     */
    public static function getFormList()
    {
        return ArrayHelper::map(self::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->select('id, name')
            ->orderBy('id')
            ->asArray()->all(), 'id', 'name');
    }

}
