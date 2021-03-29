<?php

namespace common\models\own;

use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "invoices".
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
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $version
 */
class Invoices extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoices';
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
            [['name', 'recipient', 'inn', 'kpp', 'payment_account', 'corr_account', 'bank_name', 'bik'], 'required'],
            [['name', 'recipient', 'bank_name'], 'string', 'max' => 512],
            [['inn', 'kpp', 'payment_account', 'corr_account', 'personal_account', 'bik', 'oktmo', 'kbk'], 'string', 'max' => 32],
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'version'], 'integer'],
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
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
