<?php

class m210301_150355_create_table_own extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_division', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(32)->notNull(),
        ], $tableOptions);
        $this->addCommentOnTable('guide_division','Предметные области');

        $this->db->createCommand()->batchInsert('guide_division', ['id', 'name', 'slug'], [
            [1000, 'Музыкальное искусство', 'Муз.Иск.'],
            [1001, 'Изобразительное искусство', 'Изобр.Иск.'],
            [1002, 'Хореографическое искусство', 'Хореогр.Иск.'],
            [1003, 'Театральное искусство', 'Театр.Иск.'],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_division', 1004)->execute();

        $this->createTable('guide_department', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'division_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'status' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_department','Отделы');

        $this->db->createCommand()->batchInsert('guide_department', ['id', 'division_id', 'name', 'slug', 'status'], [
            [1000, 1000, 'Фортепиано', 'Фно', 1],
            [1001, 1000, 'Струнные инструменты', 'Стр', 1],
            [1002, 1000, 'Духовые и ударные инструменты', 'Дух', 1],
            [1003, 1000, 'Народные инструменты', 'Нар', 1],
            [1004, 1000, 'Теоретические дисциплины', 'Теор', 1],
            [1005, 1000, 'Хоровое пение', 'Хор', 1],
            [1006, 1000, 'Музыкальный фольклор', 'Фольк', 1],
            [1007, 1000, 'Инструменты эстрадного оркестра', 'Джаз', 1],
            [1008, 1000, 'Отдел общего фортепиано', 'О-фно', 1],
            [1009, 1000, 'Концертмейстерский отдел', 'Конц', 1],
            [1010, 1001, 'Художественный отдел', 'Худ', 1],
            [1011, 1000, 'Отделение развития МО', 'ОР МО', 1],
            [1012, 1001, 'Класс художественной керамики', 'Керам', 1],
            [1013, 1002, 'Хореография', 'Хореография', 1],
            [1014, 1000, 'Музыкальный театр', 'Театр', 1],
            [1015, 1001, 'Архитектурное творчество', 'Арх.тв', 1],
            [1016, 1001, 'Основы дизайна', 'Диз-н', 1],
            [1017, 1000, 'Академический вокал', 'Ак.вок', 1],
            [1018, 1000, 'Сценическое мастерство', 'Сцен.маст-во', 1],
        ])->execute();

        $this->db->createCommand()->resetSequence('guide_department', 1019)->execute();
        $this->createIndex('division_id', 'guide_department', 'division_id');
        $this->addForeignKey('department_ibfk_1', 'guide_department', 'division_id', 'guide_division', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('invoices', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
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
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('invoices', 1000)->execute();
        $this->addCommentOnTable('invoices','Реквизиты');

        $this->db->createCommand()->batchInsert('invoices', ['name', 'recipient', 'inn', 'kpp', 'payment_account', 'corr_account', 'personal_account', 'bank_name', 'bik', 'oktmo', 'kbk',
        'created_at', 'updated_at', 'created_by', 'updated_by'], [
            ['Банковские реквизиты для оплаты за обучение', 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                '7733098705', '773301001', '03224643450000007300', '40102810545370000003', '2605642000830080',
                'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва', '004525988', '45367000', '05600000000131131022', time(), time(), 1000, 1000],
            ['Банковские реквизиты для добровольных пожертвований', 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                '7733098705', '773301001', '03224643450000007300', '40102810545370000003', '2605642000830080', 'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва',
                '004525988', '45367000', '05600000000155000002', time(), time(), 1000, 1000],
            ['Банковские реквизиты - Фонд поддержки и развития детского образования и культуры «МИТЮША»',
                'Фонд поддержки и развития детского образования и культуры "МИТЮША"',
                '7733092580','773301001','40703810538020100115','30101810400000000225','',
                'ПАО Сбербанк г.Москва','044525225','','', time(), time(), 1000, 1000],
        ])->execute();
    }

    public function down()
    {
        $this->dropForeignKey('department_ibfk_1', 'guide_department');
        $this->dropTableWithHistory('invoices');
        $this->dropTable('guide_department');
        $this->dropTable('guide_division');
    }
}
