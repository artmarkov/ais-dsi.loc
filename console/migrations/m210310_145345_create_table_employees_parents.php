<?php

class m210310_145345_create_table_employees_parents extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTableWithHistory('employees', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 99999)',
            'user_common_id' => $this->integer(),
            'position' => $this->string(256),
            'access_work_flag' => $this->integer()->defaultValue(0)->comment('Разрешение на доступ к работе получено'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addCommentOnTable('employees' ,'Сотрудники');
        $this->db->createCommand()->resetSequence('employees', 1000)->execute();

        $this->createTableWithHistory('parents', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 99999)',
            'user_common_id' => $this->integer(),
            'sert_name' => $this->string(32),
            'sert_series' => $this->string(32),
            'sert_num' => $this->string(32),
            'sert_organ' => $this->string(127),
            'sert_date' => $this->integer(),
            'sert_code' => $this->string(32),
            'sert_country' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('parents' ,'Родители');
        $this->db->createCommand()->resetSequence('parents', 1000)->execute();

        $this->db->createCommand()->createView('employees_view', '
         SELECT users.id AS user_id, user_common.id AS user_common_id, employees.id AS employees_id, users.username, users.email, users.status AS user_status, 
                user_common.status, user_common.last_name, user_common.first_name, user_common.middle_name, 
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM employees 
        INNER JOIN user_common ON user_common.id = employees.user_common_id 
        LEFT JOIN users ON user_common.user_id = users.id 
        WHERE user_common.user_category=\'employees\'
        ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['employees_fio', 'employees_view', 'employees_id', 'fio', 'fio', 'status', null, 'Сотрудники (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['employees_fullname', 'employees_view', 'employees_id', 'fullname', 'fullname', 'status', null, 'Сотрудники (Фамилия Имя Отчество)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['employees_users', 'employees_view', 'employees_id', 'user_id', 'employees_id', 'status', null, 'Сотрудники (ссылка на id учетной записи)'],
        ])->execute();

        $this->db->createCommand()->createView('parents_view', '
         SELECT users.id AS user_id, user_common.id AS user_common_id, parents.id AS parents_id, users.username, users.email, users.status AS user_status, 
                user_common.status, user_common.last_name, user_common.first_name, user_common.middle_name, 
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM parents 
        INNER JOIN user_common ON user_common.id = parents.user_common_id 
        LEFT JOIN users ON user_common.user_id = users.id 
        WHERE user_common.user_category=\'parents\'
        ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_fio', 'parents_view', 'parents_id', 'fio', 'fio', 'status', null, 'Родители (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_iof', 'parents_view', 'parents_id', 'iof', 'iof', 'status', null, 'Родители ( И.О. Фамилия)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_fullname', 'parents_view', 'parents_id', 'fullname', 'fullname', 'status', null, 'Родители (Фамилия Имя Отчество)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_users', 'parents_view', 'parents_id', 'user_id', 'parents_id', 'status', null, 'Родители (ссылка на id учетной записи)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_users'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_fio'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_iof'])->execute();
        $this->db->createCommand()->dropView('parents_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'employees_users'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'employees_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'employees_fio'])->execute();
        $this->db->createCommand()->dropView('employees_view')->execute();
        $this->dropTableWithHistory('parents');
        $this->dropTableWithHistory('employees');

    }
}

