<?php

use \artsoft\db\BaseMigration;

class m210316_181416_create_table_user_relation extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_user_relation', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(127),
            'slug' => $this->string(64),
        ], $tableOptions);
        $this->db->createCommand()->batchInsert('guide_user_relation', ['id', 'name', 'slug'], [
            [1, 'Мать', 'Мать'],
            [2, 'Отец', 'Отец'],
            [3, 'Бабушка', 'Баб'],
            [4, 'Дедушка', 'Дед'],
        ])->execute();

        $this->createTableWithHistory('student_dependence', [
            'id' => $this->primaryKey(),
            'relation_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'signer_flag' => $this->tinyInteger(2)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('student_id', 'student_dependence', 'student_id');
        $this->createIndex('parent_id', 'student_dependence', 'parent_id');
        $this->createIndex('relation_id', 'student_dependence', 'relation_id');

        $this->addForeignKey('student_dependence_ibfk_1', 'student_dependence', 'student_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('student_dependence_ibfk_2', 'student_dependence', 'parent_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('student_dependence_ibfk_3', 'student_dependence', 'relation_id', 'guide_user_relation', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTableWithHistory('student_dependence');
        $this->dropTable('guide_user_relation');
    }
}
