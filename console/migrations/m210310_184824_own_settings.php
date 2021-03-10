<?php

class m210310_184824_own_settings extends yii\db\Migration
{

    const TABLE_NAME = '{{%setting}}';

    public function up()
    {

        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'name', 'value' => 'Государственное бюджетное учреждение дополнительного образования г. Москвы "Детская школа искусств им. И.Ф.Стравинского"']);
            $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'shortname', 'value' => 'ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского"']);
            $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'address', 'value' => '125368, г. Москва, ул. Митинская, д. 47, кор. 1']);
            $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'email', 'value' => 'dshi13@mail.ru']);
            $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'head', 'value' => 'Карташева Н.М.']);
            $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'chief_accountant', 'value' => 'Кофанова Г.В.']);
    }

    public function down()
    {
    }

}
