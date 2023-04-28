<?php

use yii\db\Migration;

class m210301_151104_create_table_student extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_student_position', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(128),
            'slug' => $this->string(32),
            'status' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_student_position','Состояние перевода');
        $this->db->createCommand()->batchInsert('guide_student_position', ['id', 'name', 'slug', 'status'], [
            [1000, 'Принят на обучение', 'Принят', 1],
            [1001, 'Переведен в следующий класс', 'Переведен', 1],
            [1002, 'Повторение учебной программы', 'Повторение', 1],
            [1003, 'Окончание учебной программы', 'Окончание', 1],
            [1004, 'Досрочное завершение программы', 'Завершение', 1],
        ])->execute();

        $this->db->createCommand()->resetSequence('guide_student_position', 1005)->execute();

        $this->createTableWithHistory('students', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'user_common_id' => $this->integer(),
            'sert_name' => $this->string(32),
            'sert_series' => $this->string(32),
            'sert_num' => $this->string(32),
            'sert_organ' => $this->string(512),
            'sert_date' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('students' ,'Ученики');
        $this->db->createCommand()->resetSequence('students', 10000)->execute();

        $this->db->createCommand()->createView('students_view', '
         SELECT users.id AS user_id,
            user_common.id AS user_common_id,
            students.id AS students_id,
            users.username,
            users.email,
            users.status AS user_status,
            user_common.status,
            user_common.last_name,
            user_common.first_name,
            user_common.middle_name,
            user_common.birth_date,
            concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS fullname,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS fio,
            concat("left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. \', user_common.last_name) AS iof
           FROM students
             JOIN user_common ON user_common.id = students.user_common_id
             LEFT JOIN users ON user_common.user_id = users.id
          WHERE user_common.user_category::text = \'students\'::text
          ORDER BY user_common.last_name, user_common.first_name;
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['students_fio', 'students_view', 'students_id', 'fio', 'fio', 'status', null, 'Ученики (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['students_fullname', 'students_view', 'students_id', 'fullname', 'fullname', 'status', null, 'Ученики (Фамилия Имя Отчество)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['students_users', 'students_view', 'students_id', 'user_id', 'students_id', 'status', null, 'Ученики (ссылка на id учетной записи)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'students_users'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'students_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'students_fio'])->execute();
        $this->db->createCommand()->dropView('students_view')->execute();
        $this->dropTableWithHistory('students');
        $this->dropTable('guide_student_position');
    }
}
