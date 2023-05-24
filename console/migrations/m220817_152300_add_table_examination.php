<?php


class m220817_152300_add_table_examination extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_entrant_test', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'division_id' => $this->integer()->notNull(),
            'name' => $this->string()->comment('Название испытания'),
            'name_dev' => $this->string()->comment('Сокращенное название испытания'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_entrant_test', 'Справочник приемных испытаний');
        $this->addForeignKey('guide_entrant_test_ibfk_1', 'guide_entrant_test', 'division_id', 'guide_division', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->batchInsert('guide_entrant_test', ['id', 'division_id', 'name', 'name_dev', 'created_at', 'created_by', 'updated_at', 'updated_by'], [
            [1000, 1000, 'Слух', 'Сл', 10000, time(), 10000, time()],
            [1001, 1000, 'Ритм', 'Рм', 10000, time(), 10000, time()],
            [1002, 1000, 'Музыкальная память', 'МП', 10000, time(), 10000, time()],
            [1003, 1000, 'Координация движения', 'КД', 10000, time(), 10000, time()],
            [1004, 1000, 'Образное мышление', 'ОМ', 10000, time(), 10000, time()],
            [1005, 1000, 'Артистизм', 'Арт', 10000, time(), 10000, time()],
            [1006, 1001, 'Живопись', 'Жив', 10000, time(), 10000, time()],
            [1007, 1001, 'Рисунок', 'Рис', 10000, time(), 10000, time()],
            [1008, 1001, 'Композиция', 'Комп', 10000, time(), 10000, time()],
            [1009, 1001, 'Собеседование', 'Собес', 10000, time(), 10000, time()],
            [1010, 1000, 'Ритмика', 'Ри-ка', 10000, time(), 10000, time()],
            [1011, 1000, 'Сольфеджио', 'С-ф', 10000, time(), 10000, time()],
            [1012, 1000, 'Хор', 'Хор', 10000, time(), 10000, time()],
            [1013, 1000, 'Специальность', 'Спец', 10000, time(), 10000, time()],
            [1014, 1002, 'Хореографические данные', 'ХД', 10000, time(), 10000, time()],
            [1015, 1002, 'Музыкальные данные', 'МД', 10000, time(), 10000, time()],
            [1016, 1002, 'Артистичность и выразительность', 'АИВ', 10000, time(), 10000, time()],
            [1017, 1002, 'Медицинские показания', 'МП', 10000, time(), 10000, time()],
            [1018, 1000, 'Общее развитие/степень подготовки', 'ОРП', 10000, time(), 10000, time()]
        ])->execute();

        $this->db->createCommand()->resetSequence('guide_entrant_test', 1019)->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['entrant_test_name', 'guide_entrant_test', 'id', 'name', 'id', 'division_id', null, 'Приемные испытания (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['entrant_test_name_dev', 'guide_entrant_test', 'id', 'name_dev', 'id', 'division_id', null, 'Приемные испытания (кратко)'],
        ])->execute();

        $this->createTableWithHistory('entrant_comm', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'division_id' => $this->integer()->notNull(),
            'department_list' => $this->string(1024),
            'plan_year' => $this->integer()->notNull()->comment('Учебный год'),
            'name' => $this->string(127)->notNull()->comment('Название комиссии'),
            'leader_id' => $this->integer()->notNull()->comment('Реководитель комиссии user_id'),
            'secretary_id' => $this->integer()->notNull()->comment('Секретарь комиссии user_id'),
            'members_list' => $this->string(1024)->notNull()->comment('Члены комиссии user_id'),
            'prep_on_test_list' => $this->string(1024)->notNull()->comment('Список испытаний с подготовкой'),
            'prep_off_test_list' => $this->string(1024)->notNull()->comment('Список испытаний без подготовки'),
            'timestamp_in' => $this->integer()->notNull()->comment('Начало действия'),
            'timestamp_out' => $this->integer()->notNull()->comment('Окончание действия'),
            'description' => $this->string(1024)->comment('План работы комиссии'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_comm', 'Экзаменационная комиссия');
        $this->db->createCommand()->resetSequence('entrant_comm', 1000)->execute();
        $this->addForeignKey('entrant_comm_ibfk_1', 'entrant_comm', 'division_id', 'guide_division', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_comm_ibfk_2', 'entrant_comm', 'leader_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_comm_ibfk_3', 'entrant_comm', 'secretary_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_comm_ibfk_4', 'entrant_comm', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_comm_ibfk_5', 'entrant_comm', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('entrant_group', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'comm_id' => $this->integer()->notNull(),
            'name' => $this->string()->comment('Название группы'),
            'prep_flag' => $this->integer()->defaultValue(0)->comment('С подготовкой/Без подготовки'),
            'timestamp_in' => $this->integer()->notNull()->comment('Время испытания'),
            'description' => $this->string(1024)->comment('Описание группы'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_group', 'Экзаменационная группа');
        $this->db->createCommand()->resetSequence('entrant_group', 1000)->execute();
        $this->addForeignKey('entrant_group_ibfk_1', 'entrant_group', 'comm_id', 'entrant_comm', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('entrant_group_ibfk_2', 'entrant_group', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_group_ibfk_3', 'entrant_group', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('entrant', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'student_id' => $this->integer()->notNull(),
            'comm_id' => $this->integer()->notNull()->comment('Комиссия Id'),
            'group_id' => $this->integer()->notNull()->comment('Группа экзаменационная'),
            'subject_list' => $this->string(1024)->notNull()->comment('Выбранный инструмент'),
            'last_experience' => $this->string(127)->notNull()->comment('Где обучался ранее'),
            'remark' => $this->string(127)->notNull()->comment('Примечание'),
            'decision_id' => $this->integer()->comment('Решение комиссии (Рекомендован, Не рекомендован)'),
            'reason' => $this->string(1024)->comment('Причина комиссии'),
            'programm_id' => $this->integer()->comment('Назначена программа'),
            'course' => $this->integer()->comment('Назначен курс'),
            'type_id' => $this->integer()->comment('Назначен вид обучения(бюджет, внебюджет)'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Статус (В ожидании испытаний, Испытания открыты, Испытания завершены)'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant', 'Абитуриенты');
        $this->db->createCommand()->resetSequence('entrant', 10000)->execute();
        $this->addForeignKey('entrant_ibfk_1', 'entrant', 'student_id', 'students', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_ibfk_2', 'entrant', 'comm_id', 'entrant_comm', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_ibfk_3', 'entrant', 'group_id', 'entrant_group', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_ibfk_4', 'entrant', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_ibfk_5', 'entrant', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_ibfk_6', 'entrant', 'programm_id', 'education_programm', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('entrant_members', [
            'id' => $this->primaryKey(),
            'entrant_id' => $this->integer()->notNull(),
            'members_id' => $this->integer()->notNull()->comment('Член комиссии'),
            'mark_rem' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_members', 'Связь членов комиссии с поступающими');
        $this->addForeignKey('entrant_members_ibfk_1', 'entrant_members', 'entrant_id', 'entrant', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('entrant_members_ibfk_2', 'entrant_members', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_members_ibfk_3', 'entrant_members', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('entrant_test', [
            'id' => $this->primaryKey(),
            'entrant_members_id' => $this->integer()->notNull(),
            'entrant_test_id' => $this->integer()->notNull(),
            'entrant_mark_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_test', 'Испытания абитуриентов');
        $this->addForeignKey('entrant_test_ibfk_1', 'entrant_test', 'entrant_members_id', 'entrant_members', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('entrant_test_ibfk_2', 'entrant_test', 'entrant_test_id', 'guide_entrant_test', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_test_ibfk_3', 'entrant_test', 'entrant_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_test_ibfk_4', 'entrant_test', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_test_ibfk_5', 'entrant_test', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('entrant_view', '
         SELECT entrant.id,
    entrant.student_id,
    entrant.comm_id,
    entrant.group_id,
    concat(entrant_group.name, \' - \', to_char(to_timestamp(entrant_group.timestamp_in::double precision), \'DD.MM.YYYY HH24:mi\'::text)) AS group_name,
    entrant.subject_list,
    entrant.last_experience,
    entrant.decision_id,
    entrant.reason,
    entrant.programm_id,
    entrant.course,
    entrant.type_id,
    entrant.status,
    entrant_comm.timestamp_in,
    ( SELECT avg(guide_lesson_mark.mark_value) AS avg
           FROM entrant_members
             JOIN entrant_test ON entrant_test.entrant_members_id = entrant_members.id
             JOIN guide_lesson_mark ON guide_lesson_mark.id = entrant_test.entrant_mark_id
          WHERE entrant_members.entrant_id = entrant.id) AS mid_mark,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS fullname,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS fio
   FROM entrant
     JOIN students ON students.id = entrant.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN entrant_group ON entrant_group.id = entrant.group_id
     JOIN entrant_comm ON entrant_comm.id = entrant.comm_id
  ORDER BY entrant.comm_id; 
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('entrant_view')->execute();
        $this->dropTableWithHistory('entrant_test');
        $this->dropTableWithHistory('entrant_members');
        $this->dropTableWithHistory('entrant');
        $this->dropTableWithHistory('entrant_group');
        $this->dropTableWithHistory('entrant_comm');
        $this->db->createCommand()->delete('refbooks', ['name' => 'entrant_test_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'entrant_test_name'])->execute();
        $this->dropTable('guide_entrant_test');
    }
}
