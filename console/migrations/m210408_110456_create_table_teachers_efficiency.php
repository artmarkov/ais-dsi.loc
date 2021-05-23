<?php

class m210408_110456_create_table_teachers_efficiency extends \artsoft\db\BaseMigration
{
    const TABLE_NAME = 'teachers_efficiency';
    const TABLE_NAME_TREE = 'guide_efficiency_tree';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME_TREE, [
            'id' => $this->bigPrimaryKey(),
            'root' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'lvl' => $this->smallInteger(5)->notNull(),
            'name' => $this->string(512)->notNull(),
            'description' => $this->string(1024),
            'value_default' => $this->string(127),
            'icon' => $this->string(255),
            'icon_type' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'selected' => $this->boolean()->notNull()->defaultValue(false),
            'disabled' => $this->boolean()->notNull()->defaultValue(false),
            'readonly' => $this->boolean()->notNull()->defaultValue(false),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'collapsed' => $this->boolean()->notNull()->defaultValue(false),
            'movable_u' => $this->boolean()->notNull()->defaultValue(true),
            'movable_d' => $this->boolean()->notNull()->defaultValue(true),
            'movable_l' => $this->boolean()->notNull()->defaultValue(true),
            'movable_r' => $this->boolean()->notNull()->defaultValue(true),
            'removable' => $this->boolean()->notNull()->defaultValue(true),
            'removable_all' => $this->boolean()->notNull()->defaultValue(false),
            'child_allowed' => $this->boolean()->notNull()->defaultValue(true)

        ], $tableOptions);
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Дерево показателей эффективности');

        $this->createIndex('tree_NK1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex('tree_NK2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex('tree_NK3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex('tree_NK4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex('tree_NK5', self::TABLE_NAME_TREE, 'active');

        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'description', 'value_default', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed'], [
            [1, 1, 1, 2, 0, "Результативность участия учащихся и педагогических работников в мероприятиях методической и творческой напровленности", "За каждого участника Уровень мероприятия: окружной, городской, российский, международный. Подтверждающие документы: Грамоты, дипломы и пр.", 3, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [2, 2, 1, 2, 0, "Участие в организации и проведении мероприятий имеющий образовательную направленность(конференция; педагогические чтения; семинары; мастер-классы и др.)", "Документальное подтверждение участия в организации и проведении мероприятия", 10, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [3, 3, 1, 8, 0, "Выполнение творческих работ(создание партитур; переложений; оранжировок в образовательных целях в зависимости от объема)", "Фактически выполненные работы", 0, "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true],
            [4, 3, 2, 3, 1, "Выполнение творческих работ объемом 1 - 2 стр.", "", 3, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [5, 3, 4, 5, 1, "Выполнение творческих работ объемом 3 - 4 стр.", "", 10, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [6, 3, 6, 7, 1, "Выполнение творческих работ объемом 5 - 6 стр.", "", 15, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [7, 7, 1, 8, 0, "Выполнение показателей качества профессиональной деятельности", "Мониторинг качества профессиональной деятельности по результатам(успеваемость – по результатам учебного полугодия)", 0, "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true],
            [8, 7, 2, 3, 1, "Выполнение показателей качества профессиональной деятельности - отсутствие обоснованных жалоб от учащихся и родителей", "", 10, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [9, 7, 4, 5, 1, "Выполнение показателей качества профессиональной деятельности - сохранность контингента учащихся", "", 10, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [10, 7, 6, 7, 1, "Выполнение показателей качества профессиональной деятельности - отсутствие неудовлетворительных результатов промежуточной и итоговой аттестации учащихся", "", 10, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [11, 11, 1, 2, 0, "Дистанционная работа (освоение новых технологий; увеличение объема работ по проверке выполненных заданий и др.)", "Применяется при переводе всех педагогических работников на дистанционную работу по инициативе работодателя в исключительных случаях", 5, "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
        ])->execute();
        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 12)->execute();

        $this->createTableWithHistory(self::TABLE_NAME, [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 999999)',
            'class' => $this->string(),
            'item_id' => $this->integer(),
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

        $this->db->createCommand()->resetSequence(self::TABLE_NAME, 1000)->execute();
        $this->addCommentOnTable(self::TABLE_NAME, 'Показатели эффективности');
        $this->createIndex('efficiency_id', self::TABLE_NAME, 'efficiency_id');
        $this->addForeignKey('teachers_efficiency_ibfk_1', self::TABLE_NAME, 'efficiency_id', self::TABLE_NAME_TREE, 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_efficiency_ibfk_2', self::TABLE_NAME, 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropForeignKey('teachers_efficiency_ibfk_2', self::TABLE_NAME);
        $this->dropForeignKey('teachers_efficiency_ibfk_1', self::TABLE_NAME);
        $this->dropTableWithHistory(self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
