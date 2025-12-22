<?php


class m220214_204014_add_table_stadyplan_lesson extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_lesson_mark', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'mark_category' => $this->integer()->notNull(),
            'mark_label' => $this->string(8)->notNull(),
            'mark_hint' => $this->string(64),
            'mark_value' => $this->float(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_lesson_mark', 'Справочник оценок');
        $this->addForeignKey('guide_lesson_mark_ibfk_1', 'guide_lesson_mark', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('guide_lesson_mark_ibfk_2', 'guide_lesson_mark', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->batchInsert('guide_lesson_mark', ['id', 'mark_category', 'mark_label', 'mark_hint', 'mark_value', 'status', 'sort_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], [
            [1000, 2, 'ЗЧ', 'Зачет', null, 1, 10, time(), 10000, time(), 10000],
            [1001, 2, 'НЗ', 'Незачет', null, 1, 1001, time(), 10000, time(), 10000],
            [1002, 1, 'НА', 'Не аттестован', null, 1, 1002, time(), 10000, time(), 10000],
            [1003, 1, '2', null, 2, 1, 1003, time(), 10000, time(), 10000],
            [1004, 1, '3-', null, 2.6, 1, 1004, time(), 10000, time(), 10000],
            [1005, 1, '3', null, 3, 1, 1005, time(), 10000, time(), 10000],
            [1006, 1, '3+', null, 3.5, 1, 1006, time(), 10000, time(), 10000],
            [1007, 1, '4-', null, 3.6, 1, 1007, time(), 10000, time(), 10000],
            [1008, 1, '4', null, 4, 1, 1008, time(), 10000, time(), 10000],
            [1009, 1, '4+', null, 4.5, 1, 1009, time(), 10000, time(), 10000],
            [1010, 1, '5-', null, 4.6, 1, 1010, time(), 10000, time(), 10000],
            [1011, 1, '5', null, 5, 1, 1011, time(), 10000, time(), 10000],
            [1012, 1, '5+', null, 5.5, 1, 1012, time(), 10000, time(), 10000],
            [1013, 3, 'Н', 'Отсутствие по неуважительной причине', null, 1, 1013, time(), 10000, time(), 10000],
            [1014, 3, 'П', 'Отсутствие по уважительной причине', null, 1, 1014, time(), 10000, time(), 10000],
            [1015, 3, 'Б', 'Отсутствие по причине болезни', null, 1, 1015, time(), 10000, time(), 10000],
            [1016, 3, 'О', 'Опоздание на урок', null, 1, 1016, time(), 10000, time(), 10000],
            [1017, 1, '*', 'Факт присутствия(без оценки)', null, 1, 1017, time(), 10000, time(), 10000],
            [1018, 1, '1', null, 2, 1, 1018, time(), 10000, time(), 10000],
            [1019, 1, '2', null, 2.6, 1, 1019, time(), 10000, time(), 10000],
            [1020, 1, '3', null, 3, 1, 1020, time(), 10000, time(), 10000],
            [1021, 1, '4', null, 3.5, 1, 1021, time(), 10000, time(), 10000],
            [1022, 1, '5', null, 3.6, 1, 1022, time(), 10000, time(), 10000],
            [1023, 1, '6', null, 4, 1, 1023, time(), 10000, time(), 10000],
            [1024, 1, '7', null, 4.5, 1, 1024, time(), 10000, time(), 10000],
            [1025, 1, '8', null, 4.6, 1, 1025, time(), 10000, time(), 10000],
            [1026, 1, '9', null, 5, 1, 1026, time(), 10000, time(), 10000],
            [1027, 1, '10', null, 5.5, 1, 1027, time(), 10000, time(), 10000],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_lesson_mark', 1028)->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_mark', 'guide_lesson_mark', 'id', 'mark_label', 'sort_order', 'status', null, 'Список оценок'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_mark_hint', 'guide_lesson_mark', 'mark_label', 'mark_hint', 'sort_order', null, null, 'Список описаний оценок'],
        ])->execute();

        $this->createTable('guide_lesson_test', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'division_list' => $this->string(1024)->notNull(),
            'test_category' => $this->integer(),
            'test_name' => $this->string(64)->notNull(),
            'test_name_short' => $this->string(16),
            'plan_flag' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_lesson_test', 'Справочник испытаний');
        $this->addForeignKey('guide_lesson_test_ibfk_1', 'guide_lesson_test', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('guide_lesson_test_ibfk_2', 'guide_lesson_test', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->batchInsert('guide_lesson_test', ['id', 'division_list', 'test_category', 'test_name', 'test_name_short', 'plan_flag', 'status', 'sort_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], [
            [1000, '1000,1001,1002', 1, 'Текущая работа', 'ТР', 0, 1, 10000, time(), 10000, time(), 10000],
            [1001, '1001', 3, 'Итоговый просмотр (для выпускников)', 'ИП', 1, 1, 1001, time(), 10000, time(), 10000],
            [1002, '1000,1001,1002', 1, 'Контрольный урок/Зачет', 'КУ', 0, 1, 1002, time(), 10000, time(), 10000],
            [1003, '1001', 1, 'Промежуточный просмотр', 'ПП', 0, 1, 1003, time(), 10000, time(), 10000],
            [1004, '1001', 1, 'Домашнее задание', 'ДЗ', 0, 1, 1004, time(), 10000, time(), 10000],
            [1005, '1001', 1, 'Летняя работа', 'ЛР', 0, 1, 1005, time(), 10000, time(), 10000],
            [1006, '1001', 2, 'Экзаменационный просмотр', 'ЭП', 1, 1, 1006, time(), 10000, time(), 10000],
            [1007, '1001', 1, 'Реферат', 'Реф', 0, 1, 1007, time(), 10000, time(), 10000],
            [1008, '1002', 2, 'Экзамен', 'Экз.', 1, 1, 1008, time(), 10000, time(), 10000],
            [1009, '1000,1001,1002', 2, 'Промежуточная аттестация', 'ПА', 1, 1, 1010, time(), 10000, time(), 10000],
            [1010, '1000,1001,1002', 3, 'Итоговая аттестация', 'ИА', 1, 1, 1011, time(), 10000, time(), 10000],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_lesson_test', 1011)->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_test', 'guide_lesson_test', 'id', 'test_name', 'sort_order', 'status', null, 'Список испытаний'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_test_short', 'guide_lesson_test', 'id', 'test_name_short', 'sort_order', 'status', null, 'Список испытаний кор.'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_test_hint', 'guide_lesson_test', 'test_name_short', 'test_name', 'sort_order', 'test_category', null, 'Список испытаний (расшифровка)'],
        ])->execute();

        $this->createTableWithHistory('lesson_items', [
            'id' => $this->primaryKey(),
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'lesson_test_id' => $this->integer()->notNull(),
            'lesson_date' => $this->integer()->notNull(),
            'lesson_topic' => $this->string(512),
            'lesson_rem' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('lesson_items', 'Уроки дисциплин плана учащегося');

        $this->createIndex('lesson_items_subject_sect_studyplan_id', 'lesson_items', ['subject_sect_studyplan_id']);
        $this->createIndex('lesson_items_studyplan_subject_id', 'lesson_items', ['studyplan_subject_id']);
        $this->createIndex('lesson_items_lesson_test_id', 'lesson_items', ['lesson_test_id']);
        $this->createIndex('lesson_items_lesson_date', 'lesson_items', ['lesson_date']);
        $this->createIndex('lesson_items_subject_sect_studyplan_id_studyplan_subject_id_key', 'lesson_items', ['subject_sect_studyplan_id', 'studyplan_subject_id', 'lesson_test_id', 'lesson_date'], true);

        $this->addForeignKey('lesson_items_ibfk_1', 'lesson_items', 'lesson_test_id', 'guide_lesson_test', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_items_ibfk_2', 'lesson_items', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_items_ibfk_3', 'lesson_items', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('lesson_items_ibfk_4', 'lesson_items', 'subject_sect_studyplan_id', 'subject_sect_studyplan', 'id', 'CASCADE', 'CASCADE');
//        $this->addForeignKey('lesson_items_ibfk_5', 'lesson_items', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('lesson_progress', [
            'id' => $this->primaryKey(),
            'lesson_items_id' => $this->integer(),
            'studyplan_subject_id' => $this->integer(),
            'lesson_mark_id' => $this->integer(),
            'mark_rem' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createIndex('lesson_progress_lesson_items_id', 'lesson_progress', ['lesson_items_id']);
        $this->createIndex('lesson_progress_studyplan_subject_id', 'lesson_progress', ['studyplan_subject_id']);
        $this->createIndex('lesson_progress_lesson_mark_id', 'lesson_progress', ['lesson_mark_id']);
        $this->createIndex('lesson_progress_lesson_items_id_studyplan_subject_id_lesson_key', 'lesson_progress', ['lesson_items_id','studyplan_subject_id'], true);

        $this->addForeignKey('lesson_progress_ibfk_1', 'lesson_progress', 'lesson_items_id', 'lesson_items', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('lesson_progress_ibfk_2', 'lesson_progress', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('lesson_progress_ibfk_3', 'lesson_progress', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_progress_ibfk_4', 'lesson_progress', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_progress_ibfk_5', 'lesson_progress', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('lesson_progress_view', '
   SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    0 AS subject_sect_id,
    studyplan.plan_year,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \', \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', guide_subject_type.slug, \' \', education_programm.short_name, \')\') AS student_fullname,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\', \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', guide_subject_type.slug, \' \', education_programm.short_name, \')\') AS student_fio,
    array_to_string(ARRAY( SELECT teachers_load.teachers_id
           FROM teachers_load
          WHERE teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0), \',\'::text) AS teachers_list,
    \'Индивидуально\'::text AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    concat(studyplan_subject.subject_id, \'|\', studyplan_subject.subject_vid_id, \'|\', studyplan_subject.subject_type_id, \'|\', education_programm.education_cat_id) AS subject_key,
    studyplan.status,
    studyplan.status_reason,
    guide_education_cat.id AS education_cat_id,
    studyplan_subject.med_cert,
    studyplan_subject.fin_cert
   FROM studyplan_subject
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \', \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', guide_subject_type_sub.slug, \' \', education_programm.short_name, \')\') AS student_fullname,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\', \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', guide_subject_type_sub.slug, \' \', education_programm.short_name, \')\') AS student_fio,
    array_to_string(ARRAY( SELECT teachers_load.teachers_id
           FROM teachers_load
          WHERE teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0), \',\'::text) AS teachers_list,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \' \', guide_subject_type_sect.slug, \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \') \') AS subject,
    NULL::text AS subject_key,
    studyplan.status,
    studyplan.status_reason,
    guide_education_cat.id AS education_cat_id,
    studyplan_subject.med_cert,
    studyplan_subject.fin_cert
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     JOIN studyplan_subject ON studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[])
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = subject_sect.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_category ON guide_subject_category.id = subject_sect.subject_cat_id
     LEFT JOIN guide_subject_type guide_subject_type_sect ON guide_subject_type_sect.id = subject_sect_studyplan.subject_type_id
     LEFT JOIN guide_subject_type guide_subject_type_sub ON guide_subject_type_sub.id = studyplan_subject.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
  ORDER BY 9, 10, 7;
        ')->execute();

        $this->db->createCommand()->createView('lesson_items_progress_view', '
   SELECT 0 AS subject_sect_studyplan_id,
    lesson_progress.studyplan_subject_id,
    studyplan_subject.studyplan_id,
    0 AS subject_sect_id,
    lesson_items.id AS lesson_items_id,
    lesson_items.lesson_test_id,
    lesson_items.lesson_date,
    lesson_items.lesson_topic,
    lesson_items.lesson_rem,
    lesson_progress.id AS lesson_progress_id,
    lesson_progress.lesson_mark_id,
    guide_lesson_test.test_category,
    guide_lesson_test.test_name,
    guide_lesson_test.test_name_short,
    guide_lesson_test.plan_flag,
    guide_lesson_mark.mark_category,
    guide_lesson_mark.mark_label,
    guide_lesson_mark.mark_hint,
    guide_lesson_mark.mark_value,
    lesson_progress.mark_rem,
    array_to_string(ARRAY( SELECT teachers_load.teachers_id
           FROM teachers_load
          WHERE teachers_load.studyplan_subject_id = lesson_items.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0), \',\'::text) AS teachers_list,
    concat(studyplan_subject.subject_id, \'|\', studyplan_subject.subject_vid_id, \'|\', studyplan_subject.subject_type_id, \'|\', education_programm.education_cat_id) AS subject_key,
    studyplan.status, 
	studyplan.status_reason,
    studyplan.plan_year,
    studyplan_subject.subject_id,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_cat_id
   FROM lesson_items
     JOIN lesson_progress ON lesson_progress.lesson_items_id = lesson_items.id AND lesson_items.subject_sect_studyplan_id = 0
     JOIN studyplan_subject ON studyplan_subject.id = lesson_progress.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     LEFT JOIN guide_lesson_test ON guide_lesson_test.id = lesson_items.lesson_test_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = lesson_progress.lesson_mark_id
UNION ALL
 SELECT lesson_items.subject_sect_studyplan_id,
    lesson_progress.studyplan_subject_id,
    studyplan_subject.studyplan_id,
    subject_sect.id AS subject_sect_id,
    lesson_items.id AS lesson_items_id,
    lesson_items.lesson_test_id,
    lesson_items.lesson_date,
    lesson_items.lesson_topic,
    lesson_items.lesson_rem,
    lesson_progress.id AS lesson_progress_id,
    lesson_progress.lesson_mark_id,
    guide_lesson_test.test_category,
    guide_lesson_test.test_name,
    guide_lesson_test.test_name_short,
    guide_lesson_test.plan_flag,
    guide_lesson_mark.mark_category,
    guide_lesson_mark.mark_label,
    guide_lesson_mark.mark_hint,
    guide_lesson_mark.mark_value,
    lesson_progress.mark_rem,
    array_to_string(ARRAY( SELECT teachers_load.teachers_id
           FROM teachers_load
          WHERE teachers_load.subject_sect_studyplan_id = lesson_items.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0), \',\'::text) AS teachers_list,
    NULL::text AS subject_key,
    studyplan.status, 
	studyplan.status_reason,
    studyplan.plan_year,
    studyplan_subject.subject_id,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_cat_id
   FROM lesson_items
     LEFT JOIN lesson_progress ON lesson_progress.lesson_items_id = lesson_items.id AND lesson_items.studyplan_subject_id = 0
     JOIN studyplan_subject ON studyplan_subject.id = lesson_progress.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     LEFT JOIN guide_lesson_test ON guide_lesson_test.id = lesson_items.lesson_test_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = lesson_progress.lesson_mark_id
  ORDER BY 1, 2, 7, 6;
        ')->execute();

        $this->db->createCommand()->createView('lesson_items_progress_studyplan_view', '
   SELECT 0 AS subject_sect_studyplan_id,
    lesson_progress.studyplan_subject_id,
    studyplan_subject.studyplan_id,
    0 AS subject_sect_id,
    lesson_items.id AS lesson_items_id,
    lesson_items.lesson_date,
    lesson_items.lesson_topic,
    lesson_items.lesson_rem,
    lesson_progress.id AS lesson_progress_id,
    lesson_progress.lesson_mark_id,
    guide_lesson_test.test_category,
    guide_lesson_test.test_name,
    guide_lesson_test.test_name_short,
    guide_lesson_test.plan_flag,
    guide_lesson_mark.mark_category,
    guide_lesson_mark.mark_label,
    guide_lesson_mark.mark_hint,
    guide_lesson_mark.mark_value,
    lesson_progress.mark_rem,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    concat(studyplan_subject.subject_id, \'|\', studyplan_subject.subject_vid_id, \'|\', studyplan_subject.subject_type_id, \'|\', education_programm.education_cat_id) AS subject_key,
    studyplan.status,
    studyplan.status_reason,
    studyplan.plan_year
   FROM lesson_items
     JOIN lesson_progress ON lesson_progress.lesson_items_id = lesson_items.id AND lesson_items.subject_sect_studyplan_id = 0
     JOIN teachers_load ON teachers_load.studyplan_subject_id = lesson_items.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN studyplan_subject ON studyplan_subject.id = lesson_progress.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     LEFT JOIN guide_lesson_test ON guide_lesson_test.id = lesson_items.lesson_test_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = lesson_progress.lesson_mark_id
UNION ALL
 SELECT lesson_items.subject_sect_studyplan_id,
    lesson_progress.studyplan_subject_id,
    studyplan_subject.studyplan_id,
    subject_sect.id AS subject_sect_id,
    lesson_items.id AS lesson_items_id,
    lesson_items.lesson_date,
    lesson_items.lesson_topic,
    lesson_items.lesson_rem,
    lesson_progress.id AS lesson_progress_id,
    lesson_progress.lesson_mark_id,
    guide_lesson_test.test_category,
    guide_lesson_test.test_name,
    guide_lesson_test.test_name_short,
    guide_lesson_test.plan_flag,
    guide_lesson_mark.mark_category,
    guide_lesson_mark.mark_label,
    guide_lesson_mark.mark_hint,
    guide_lesson_mark.mark_value,
    lesson_progress.mark_rem,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    NULL::text AS subject_key,
    studyplan.status,
    studyplan.status_reason,
    studyplan.plan_year
   FROM lesson_items
     JOIN lesson_progress ON lesson_progress.lesson_items_id = lesson_items.id AND lesson_items.studyplan_subject_id = 0
     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = lesson_items.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0
     JOIN studyplan_subject ON studyplan_subject.id = lesson_progress.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     LEFT JOIN guide_lesson_test ON guide_lesson_test.id = lesson_items.lesson_test_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = lesson_progress.lesson_mark_id
  ORDER BY 1, 2, 6;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('lesson_items_progress_studyplan_view')->execute();
        $this->db->createCommand()->dropView('lesson_items_progress_view')->execute();
        $this->db->createCommand()->dropView('lesson_progress_view')->execute();
        $this->dropTableWithHistory('lesson_progress');
        $this->dropTableWithHistory('lesson_items');
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_test_hint'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_test_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_test'])->execute();
        $this->dropTable('guide_lesson_test');
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_mark_hint'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_mark'])->execute();
        $this->dropTable('guide_lesson_mark');

    }
}
