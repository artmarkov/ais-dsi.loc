<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $full_name
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property string $postal_code
 */
class Address extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address_line1', 'address_line2', 'city', 'state', 'postal_code'], 'required'],
            [['customer_id'], 'integer'],
            [['full_name'], 'string', 'max' => 128],
            [['address_line1', 'address_line2'], 'string', 'max' => 256],
            [['city', 'state', 'postal_code'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'full_name' => 'Full Name',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
        ];
    }
    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
