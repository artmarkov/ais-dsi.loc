<?php

namespace common\models;

use Yii;
use cranky4\changeLogBehavior\ChangeLogBehavior;

/**
 * This is the model class for table "option".
 *
 * @property int $id
 * @property string $name
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'option';
    }

    /**
     * поведение отвечает за запись изменений полей модели (table: changelogs)
     */
    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::className(),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }
}
