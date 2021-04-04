<?php

class m150319_184824_init_settings extends yii\db\Migration
{
    const TABLE_NAME = 'setting';

    public function up()
    {
        $tableOptions = null;

        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'group' => $this->string(64)->defaultValue('general'),
            'key' => $this->string(64)->notNull(),
            'language' => $this->string(6),
            'value' => $this->text(),
            'description' => $this->text(),
        ], $tableOptions);

        $this->addCommentOnTable(self::TABLE_NAME ,'Настройки');
        $this->createIndex('setting_group_lang', self::TABLE_NAME, ['group', 'key', 'language']);

        $this->insert(self::TABLE_NAME, ['group' => 'general', 'key' => 'title', 'value' => 'АИС "Школа искусств']);
        $this->insert(self::TABLE_NAME, ['group' => 'general', 'key' => 'email', 'value' => 'admin@stravinskiy.ru']);
        $this->insert(self::TABLE_NAME, ['group' => 'general', 'key' => 'timezone', 'value' => 'Europe/London']);
        $this->insert(self::TABLE_NAME, ['group' => 'general', 'key' => 'dateformat', 'value' => 'dd.MM.yyyy']);
        $this->insert(self::TABLE_NAME, ['group' => 'general', 'key' => 'timeformat', 'value' => 'HH:mm']);

        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'page_size', 'value' => '20']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'phone_mask', 'value' => '+7 (999) 999 99 99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'date_mask', 'value' => '99.99.9999']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'time_mask', 'value' => '99:99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'date_time_mask', 'value' => '99.99.9999 99:99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'snils_mask', 'value' => '999.999.999 99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'coordinate_mask', 'value' => '99.99999']);

        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'name', 'value' => 'Государственное бюджетное учреждение дополнительного образования г. Москвы "Детская школа искусств им. И.Ф.Стравинского"']);
        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'shortname', 'value' => 'ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского"']);
        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'address', 'value' => '125368, г. Москва, ул. Митинская, д. 47, кор. 1']);
        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'email', 'value' => 'dshi13@mail.ru']);
        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'head', 'value' => 'Карташева Н.М.']);
        $this->insert(self::TABLE_NAME, ['group' => 'own', 'key' => 'chief_accountant', 'value' => 'Кофанова Г.В.']);

    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }

}
