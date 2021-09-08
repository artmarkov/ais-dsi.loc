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
            [5, 'Брат', 'Баб'],
            [6, 'Сестра', 'Дед'],
            [7, 'Опекун', 'Опек'],
            [8, 'Официальный представитель', 'Офиц.пр.'],
        ])->execute();

        $this->createTableWithHistory('student_dependence', [
            'id' => $this->primaryKey(),
            'relation_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('student_id', 'student_dependence', 'student_id');
        $this->createIndex('parent_id', 'student_dependence', 'parent_id');
        $this->createIndex('relation_id', 'student_dependence', 'relation_id');

        $this->addForeignKey('student_dependence_ibfk_1', 'student_dependence', 'student_id', 'students', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('student_dependence_ibfk_2', 'student_dependence', 'parent_id', 'parents', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('student_dependence_ibfk_3', 'student_dependence', 'relation_id', 'guide_user_relation', 'id', 'RESTRICT', 'RESTRICT');

        $this->db->createCommand()->createView('parents_dependence_view', '
         SELECT student_id, 
                parent_id,
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM students 
        INNER JOIN student_dependence ON student_dependence.student_id = students.id 
		INNER JOIN parents ON student_dependence.parent_id = parents.id 
		INNER JOIN user_common ON user_common.id = parents.user_common_id
		WHERE user_common.user_category=\'parents\' 
		ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_dependence_fio', 'parents_dependence_view', 'parent_id', 'fio', 'fio', 'student_id', null, 'Родители (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_dependence_iof', 'parents_dependence_view', 'parent_id', 'iof', 'fio', 'student_id', null, 'Родители ( И.О. Фамилия)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_dependence_fullname', 'parents_dependence_view', 'parent_id', 'fullname', 'fullname', 'student_id', null, 'Родители (Фамилия Имя Отчество)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_dependence_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_dependence_iof'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_dependence_fio'])->execute();
        $this->db->createCommand()->dropView('parents_dependence_view')->execute();

        $this->dropTableWithHistory('student_dependence');
        $this->dropTable('guide_user_relation');
    }
}
