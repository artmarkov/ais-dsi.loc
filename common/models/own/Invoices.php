<?php

namespace common\models\own;

use Yii;

/**
 * This is the model class for table "{{%invoices}}".
 *
 * @property int $id
 * @property string $name
 * @property string $recipient
 * @property string $inn
 * @property string $kpp
 * @property string $payment_account
 * @property string $corr_account
 * @property string $personal_account
 * @property string $bank_name
 * @property string $bik
 * @property string $oktmo
 * @property string $kbk
 */
class Invoices extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%invoices}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'recipient', 'inn', 'kpp', 'payment_account', 'corr_account', 'bank_name', 'bik'], 'required'],
            [['name', 'recipient', 'bank_name'], 'string', 'max' => 512],
            [['inn', 'kpp', 'payment_account', 'corr_account', 'personal_account', 'bik', 'oktmo', 'kbk'], 'string', 'max' => 32],
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
            'recipient' => Yii::t('art/guide', 'Recipient'),
            'inn' => Yii::t('art/guide', 'Inn'),
            'kpp' => Yii::t('art/guide', 'Kpp'),
            'payment_account' => Yii::t('art/guide', 'Payment Account'),
            'corr_account' => Yii::t('art/guide', 'Corr Account'),
            'personal_account' => Yii::t('art/guide', 'Personal Account'),
            'bank_name' => Yii::t('art/guide', 'Bank Name'),
            'bik' => Yii::t('art/guide', 'Bik'),
            'oktmo' => Yii::t('art/guide', 'Oktmo'),
            'kbk' => Yii::t('art/guide', 'Kbk'),
        ];
    }
}
