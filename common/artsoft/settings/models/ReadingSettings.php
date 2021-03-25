<?php

namespace artsoft\settings\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class ReadingSettings extends BaseSettingsModel
{
    const GROUP = 'reading';

    public $page_size;
    public $phone_mask;
    public $date_mask;
    public $time_mask;
    public $date_time_mask;
    public $snils_mask;
    public $coordinate_mask;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                ['page_size', 'required'],
                [['page_size'], 'integer'],
                [['phone_mask', 'date_mask', 'time_mask', 'date_time_mask', 'snils_mask', 'coordinate_mask'], 'string'],
                ['page_size', 'default', 'value' => 10],
                ['phone_mask', 'default', 'value' => '+7 (999) 999 99 99'],
                ['date_mask', 'default', 'value' => '99.99.9999'],
                ['time_mask', 'default', 'value' => '99:99'],
                ['date_time_mask', 'default', 'value' => '99.99.9999 99:99'],
                ['snils_mask', 'default', 'value' => '999.999.999 99'],
                ['coordinate_mask', 'default', 'value' => '99.99999'],
        ]);
    }


    public function attributeLabels()
    {
        return [
            'page_size' => Yii::t('art/settings', 'Page Size'),
            'phone_mask' => Yii::t('art/settings', 'Phone Mask'),
            'date_mask' => Yii::t('art/settings', 'Date Mask'),
            'time_mask' => Yii::t('art/settings', 'Time Mask'),
            'date_time_mask' => Yii::t('art/settings', 'Date & Time Mask'),
            'snils_mask' => Yii::t('art/settings', 'Snils Mask'),
            'coordinate_mask' => Yii::t('art/settings', 'Coordinate Mask'),
        ];
    }

}