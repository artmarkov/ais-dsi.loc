<?php

class m210406_145345_create_table_employees_parents extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTableWithHistory('employees', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_common_id' => $this->integer(),
            'position' => $this->string(256),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('employees', 1000)->execute();

        $this->createTableWithHistory('parents', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_common_id' => $this->integer(),
            'sert_name' => $this->string(32),
            'sert_series' => $this->string(32),
            'sert_num' => $this->string(32),
            'sert_organ' => $this->string(127),
            'sert_date' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('parents', 1000)->execute();
    }

    public function down()
    {
        $this->dropTableWithHistory('parents');
        $this->dropTableWithHistory('employees');

    }
}

