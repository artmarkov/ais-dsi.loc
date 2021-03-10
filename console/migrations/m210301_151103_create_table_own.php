<?php

use yii\db\Migration;

class m210301_151103_create_table_own extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%division}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(32)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%department}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'division_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('division_id', '{{%department}}', 'division_id');
        $this->addForeignKey('department_ibfk_1', '{{%department}}', 'division_id', '{{%division}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%invoices}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(512)->notNull(),
            'recipient' => $this->string(512)->notNull(),
            'inn' => $this->string(32)->notNull(),
            'kpp' => $this->string(32)->notNull(),
            'payment_account' => $this->string(32)->notNull(),
            'corr_account' => $this->string(32)->notNull(),
            'personal_account' => $this->string(32),
            'bank_name' => $this->string(512)->notNull(),
            'bik' => $this->string(32)->notNull(),
            'oktmo' => $this->string(32),
            'kbk' => $this->string(32),
        ], $tableOptions);


        $this->db->createCommand()->batchInsert('{{%invoices}}', ['id', 'name', 'recipient', 'inn', 'kpp', 'payment_account', 'corr_account', 'personal_account', 'bank_name', 'bik', 'oktmo', 'kbk'
        ], [
            [1, 'Банковские реквизиты для оплаты за обучение', 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                '7733098705', '773301001', '03224643450000007300', '40102810545370000003', '2605642000830080',
                'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва', '004525988', '45367000', '05600000000131131022'],
            [2, 'Банковские реквизиты для добровольных пожертвований', 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                '7733098705', '773301001', '03224643450000007300', '40102810545370000003', '2605642000830080', 'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва',
                '004525988', '45367000', '05600000000155000002'],
            [3, 'Банковские реквизиты - Фонд поддержки и развития детского образования и культуры «МИТЮША»',
                'Фонд поддержки и развития детского образования и культуры "МИТЮША"',
                '7733092580','773301001','40703810538020100115','30101810400000000225','',
                'ПАО Сбербанк г.Москва','044525225','',''],
        ])->execute();
    }

    public function down()
    {
        $this->dropTable('{{%invoices}}');
        $this->dropTable('{{%department}}');
        $this->dropTable('{{%division}}');
    }
}
