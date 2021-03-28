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
            'id' => $this->primaryKey(8),
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(32)->notNull(),
        ], $tableOptions);
        $this->addCommentOnTable('guide_division','Отделения');

        $this->db->createCommand()->batchInsert('guide_division', ['id', 'name', 'slug'], [
            [1, 'Музыкальное отделение', 'МО'],
            [2, 'Художественное отделение', 'ИЗО'],
            [3, 'Отделение "Хореография"', 'ХО'],
        ])->execute();

        $this->createTable('guide_department', [
            'id' => $this->primaryKey(8),
            'division_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_department','Отделы');

        $this->db->createCommand()->batchInsert('guide_department', ['id', 'division_id', 'name', 'slug', 'status'], [
            [2, 1, 'Фортепиано', 'Фно', 1],
            [3, 1, 'Струнные инструменты', 'Стр', 1],
            [4, 1, 'Духовые и ударные инструменты', 'Дух', 1],
            [5, 1, 'Народные инструменты', 'Нар', 1],
            [6, 1, 'Теоретические дисциплины', 'Теор', 1],
            [7, 1, 'Хоровое пение', 'Хор', 1],
            [8, 1, 'Музыкальный фольклор', 'Фольк', 1],
            [9, 1, 'Инструменты эстрадного оркестра', 'Джаз', 1],
            [10, 1, 'Отдел общего фортепиано', 'О-фно', 1],
            [11, 1, 'Концертмейстерский отдел', 'Конц', 1],
            [12, 2, 'Художественный отдел', 'Худ', 1],
            [13, 1, 'Отделение развития МО', 'ОР МО', 1],
            [14, 2, 'Класс художественной керамики', 'Керам', 1],
            [15, 3, 'Хореография', 'Хореография', 1],
            [16, 1, 'Музыкальный театр', 'Театр', 1],
            [17, 2, 'Архитектурное творчество', 'Арх.тв', 1],
            [18, 2, 'Основы дизайна', 'Диз-н', 1],
            [19, 1, 'Академический вокал', 'Ак.вок', 1],
            [20, 1, 'Сценическое мастерство', 'Сцен.маст-во', 1],
        ])->execute();

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
                'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва', '004525988', '45367000', '05600000000131131022', time(), time(), 1, 1],
            ['Банковские реквизиты для добровольных пожертвований', 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                '7733098705', '773301001', '03224643450000007300', '40102810545370000003', '2605642000830080', 'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва',
                '004525988', '45367000', '05600000000155000002', time(), time(), 1, 1],
            ['Банковские реквизиты - Фонд поддержки и развития детского образования и культуры «МИТЮША»',
                'Фонд поддержки и развития детского образования и культуры "МИТЮША"',
                '7733092580','773301001','40703810538020100115','30101810400000000225','',
                'ПАО Сбербанк г.Москва','044525225','','', time(), time(), 1, 1],
        ])->execute();
    }

    public function down()
    {
        $this->dropTable('invoices');
        $this->dropTable('guide_department');
        $this->dropTable('guide_division');
    }
}
