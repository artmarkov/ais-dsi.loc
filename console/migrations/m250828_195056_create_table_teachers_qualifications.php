<?php

class m250828_195056_create_table_teachers_qualifications extends \artsoft\db\BaseMigration
{
    const TABLE_NAME = 'teachers_qualifications';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory(self::TABLE_NAME, [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 999999)',
            'teachers_id' => $this->integer()->notNull(),
            'name' => $this->string(254)->notNull(),
            'place' => $this->string(512)->notNull(),
            'description' => $this->string(1024),
            'date' => $this->integer(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence(self::TABLE_NAME, 1000)->execute();
        $this->addCommentOnTable(self::TABLE_NAME, 'Показатели ППК');
        $this->addForeignKey('teachers_qualifications_ibfk_1', self::TABLE_NAME, 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropForeignKey('teachers_qualifications_ibfk_1', self::TABLE_NAME);
        $this->dropTableWithHistory(self::TABLE_NAME);
    }
}
