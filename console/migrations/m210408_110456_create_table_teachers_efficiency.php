<?php

class m210408_110456_create_table_teachers_efficiency extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_efficiency_bonus', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'name' => $this->string(512),
            'description' => $this->string(1024),
            'value_default' => $this->string(127),
        ], $tableOptions);

        $this->addForeignKey('guide_efficiency_parentid_fk', 'guide_efficiency_bonus', 'parent_id', 'guide_efficiency_bonus', 'id');
        $this->addCommentOnTable('guide_efficiency_bonus', 'Дерево показателей эффективности');

        $this->db->createCommand()->createView('guide_efficiency_bonus_childs', '
         with recursive r as (
           select id, parent_id, id as root_id from guide_efficiency_bonus
           union all
           select t.id, t.parent_id, r.root_id from guide_efficiency_bonus t, r where t.parent_id=r.id
         )
         select root_id, id from r order by root_id,id
        ')->execute();

        $this->db->createCommand()->batchInsert('guide_efficiency_bonus', ['id', 'parent_id', 'name', 'description', 'value_default'], [
            [1, NULL, 'Результативность участия учащихся и педагогических работников в мероприятиях методической и творческой напровленности', 'За каждого участника Уровень мероприятия: окружной, городской, российский, международный - 3%. Подтверждающие документы: Грамоты, дипломы и пр.', '3'],
            [2, NULL, 'Участие в организации и проведении мероприятий, имеющий образовательную направленность(конференция, педагогические чтения, семинары, мастер-классы и др.)', 'Документальное подтверждение участия в организации и проведении мероприятия', '10'],
            [3, NULL, 'Выполнение творческих работ: создание партитур, переложений, оранжировок в образовательных целях в зависимости от объема', 'Фактически выполненные работы', ''],
            [4, NULL, 'Выполнение показателей качества профессиональной деятельности', 'Мониторинг качества профессиональной деятельности по результатам (успеваемость – по результатам учебного полугодия)', ''],
            [5, NULL, 'Инклюзивное обучение детей с различными образовательными возможностями с применением особого подхода, методики', 'За ученика', '5'],
            [6, NULL, 'Дистанционная работа (освоение новых технологий, увеличение объема работ по проверке выполненных заданий и др.)', 'Применяется, при переводе всех педагогических работников на дистанционную работу по инициативе работодателя в исключительных случаях', '5'],
            [7, 3, '1-2 стр.', '', '3'],
            [8, 3, '3-4 стр.', '', '10'],
            [9, 3, '5-6 стр.', '', '15'],
            [10, 4, 'отсутствие обоснованных жалоб от учащихся и родителей', '', '10'],
            [11, 4, 'сохранность контингента учащихся', '', '10'],
            [12, 4, 'отсутствие неудовлетворительных результатов промежуточной и итоговой аттестации учащихся', '', '10'],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_efficiency_bonus', 13)->execute();

        $this->createTableWithHistory('teachers_efficiency', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'efficiency_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'bonus' => $this->string(127),
            'date_in' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('teachers_efficiency', 1000)->execute();
        $this->addCommentOnTable('teachers_efficiency', 'Показатели эффективности');
        $this->createIndex('efficiency_id', 'teachers_efficiency', 'efficiency_id');
        $this->addForeignKey('teachers_efficiency_ibfk_1', 'teachers_efficiency', 'efficiency_id', 'guide_efficiency_bonus', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_efficiency_ibfk_2', 'teachers_efficiency', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->db->createCommand()->dropView('guide_efficiency_bonus_childs')->execute();
        $this->dropForeignKey('guide_efficiency_parentid_fk', 'guide_efficiency_bonus');
        $this->dropForeignKey('teachers_efficiency_ibfk_2', 'teachers_efficiency');
        $this->dropForeignKey('teachers_efficiency_ibfk_1', 'teachers_efficiency');
        $this->dropTableWithHistory('teachers_efficiency');
        $this->dropTable('guide_efficiency_bonus');
    }
}
