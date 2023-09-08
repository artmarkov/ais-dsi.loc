<?php


class m220210_160814_add_table_stadyplan_thematic extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_piece_category', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(256)->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_piece_category', 'Категории музыкальных произведений');
        $this->db->createCommand()->resetSequence('guide_piece_category', 1000)->execute();

        $this->db->createCommand()->batchInsert('guide_piece_category', ['name', 'description', 'sort_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], [
            ['Крупная форма', '', 4, time(), 1000, time(), 1000],
            ['Полифония', '', 3, time(), 1000, time(), 1000],
            ['Этюд', '', 2, time(), 1000, time(), 1000],
            ['Пьеса', '', 5, time(), 1000, time(), 1000],
            ['Ансамбль', '', 6, time(), 1000, time(), 1000],
            ['Гаммы и упражнения', '', 1, time(), 1000, time(), 1000],
            ['Песня', '', 5, time(), 1000, time(), 1000],
            ['Вокальный дуэт', '', 6, time(), 1000, time(), 1000],
            ['Аккомпанемент', '', 7, time(), 1000, time(), 1000],
            ['Обработка народной мелодии', '', 8, time(), 1000, time(), 1000],
        ])->execute();


        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['piece_category', 'guide_piece_category', 'id', 'name', 'sort_order', 'status', null, 'Список категорий муз. произведений'],
        ])->execute();

        $this->createTable('studyplan_thematic', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'thematic_category' => $this->integer()->notNull(),
            'half_year' => $this->integer()->notNull()->defaultValue(0),
            'template_flag' => $this->integer()->defaultValue(0),
            'template_name' => $this->string(256),
            'doc_status' => $this->integer(),
            'author_id' => $this->integer(),
            'doc_sign_teachers_id' => $this->integer(),
            'doc_sign_timestamp' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('studyplan_thematic_ibfk_1', 'studyplan_thematic', 'doc_sign_teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('studyplan_thematic_ibfk_2', 'studyplan_thematic', 'subject_sect_studyplan_id', 'subject_sect_studyplan', 'id', 'CASCADE', 'CASCADE');
//        $this->addForeignKey('studyplan_thematic_ibfk_3', 'studyplan_thematic', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');

        $this->addCommentOnTable('studyplan_thematic', 'Тематические планы инд. плана ученика');
        $this->db->createCommand()->resetSequence('studyplan_thematic', 1000)->execute();

        $this->createTable('studyplan_thematic_items', [
            'id' => $this->primaryKey(),
            'studyplan_thematic_id' => $this->integer(),
            'piece_category_id' => $this->integer(),
            'author' => $this->string(256),
            'piece_name' => $this->string(256),
            'task' => $this->string(1024)->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('studyplan_thematic_items', 'Тематические планы инд. плана ученика(содержание)');
        $this->createIndex('studyplan_thematic_id', 'studyplan_thematic_items', 'studyplan_thematic_id');
        $this->addForeignKey('studyplan_thematic_items_ibfk_1', 'studyplan_thematic_items', 'studyplan_thematic_id', 'studyplan_thematic', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('studyplan_thematic_items_ibfk_2', 'studyplan_thematic_items', 'piece_category_id', 'guide_piece_category', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('thematic_view', '
          SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    0 AS subject_sect_id,
    studyplan.plan_year,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.thematic_category,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.studyplan_subject_id = studyplan_subject.id AND studyplan_thematic.subject_sect_studyplan_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT 0 AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    subject_sect_studyplan.subject_type_id,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.thematic_category,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     LEFT JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0
     JOIN subject ON subject.id = subject_sect.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = subject_sect.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.subject_sect_studyplan_id = subject_sect_studyplan.id AND studyplan_thematic.studyplan_subject_id = 0
  ORDER BY 18, 17;  		   
        ')->execute();
        $this->db->createCommand()->createView('studyplan_thematic_view', '
            SELECT studyplan.id AS studyplan_id,
    studyplan.student_id,
    studyplan.plan_year,
    studyplan.programm_id,
    studyplan.course,
    studyplan.status,
    studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.subject_cat_id,
    studyplan_subject.subject_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_vid_id,
	teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.subject_sect_studyplan_id,
    studyplan_thematic.thematic_category,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
	 LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.studyplan_subject_id = studyplan_subject.id AND studyplan_thematic.subject_sect_studyplan_id = 0
	 JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id

UNION ALL
 SELECT studyplan.id AS studyplan_id,
    studyplan.student_id,
    studyplan.plan_year,
    studyplan.programm_id,
    studyplan.course,
    studyplan.status,
    studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.subject_cat_id,
    studyplan_subject.subject_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_vid_id,
	teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    studyplan_thematic.thematic_category,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan_subject.studyplan_id = studyplan.id
	 LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.subject_sect_studyplan_id = subject_sect_studyplan.id AND studyplan_thematic.studyplan_subject_id = 0
  ORDER BY 22, 21;
  		   
        ')->execute();

    }

    public function down()
    {
        $this->db->createCommand()->dropView('studyplan_thematic_view')->execute();
        $this->dropTable('studyplan_thematic_items');
        $this->dropTable('studyplan_thematic');
        $this->dropTable('guide_piece_category');
        $this->db->createCommand()->delete('refbooks', ['name' => 'piece_category'])->execute();

    }
}
