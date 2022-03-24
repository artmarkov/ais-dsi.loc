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
            [1000, 2, 'ЗЧ', 'Зачет', null, 1, 10, time(), 1000, time(), 1000],
            [1001, 2, 'НЗ', 'Незачет', null, 1, 1001, time(), 1000, time(), 1000],
            [1002, 1, 'НА', 'Не аттестован', null, 1, 1002, time(), 1000, time(), 1000],
            [1003, 1, '2', null, 2, 1, 1003, time(), 1000, time(), 1000],
            [1004, 1, '3-', null, 2.6, 1, 1004, time(), 1000, time(), 1000],
            [1005, 1, '3', null, 3, 1, 1005, time(), 1000, time(), 1000],
            [1006, 1, '3+', null, 3.5, 1, 1006, time(), 1000, time(), 1000],
            [1007, 1, '4-', null, 3.6, 1, 1007, time(), 1000, time(), 1000],
            [1008, 1, '4', null, 4, 1, 1008, time(), 1000, time(), 1000],
            [1009, 1, '4+', null, 4.5, 1, 1009, time(), 1000, time(), 1000],
            [1010, 1, '5-', null, 4.6, 1, 1010, time(), 1000, time(), 1000],
            [1011, 1, '5', null, 5, 1, 1011, time(), 1000, time(), 1000],
            [1012, 1, '5+', null, 5.5, 1, 1012, time(), 1000, time(), 1000],
            [1013, 3, 'Н', 'Отсутствие по неуважительной причине', null, 1, 1013, time(), 1000, time(), 1000],
            [1014, 3, 'П', 'Отсутствие по уважительной причине', null, 1, 1014, time(), 1000, time(), 1000],
            [1015, 3, 'Б', 'Отсутствие по причине болезни', null, 1, 1015, time(), 1000, time(), 1000],
            [1016, 3, 'О', 'Опоздание на урок', null, 1, 1016, time(), 1000, time(), 1000],
            [1017, 1, '*', 'Факт присутствия(без оценки)', null, 1, 1017, time(), 1000, time(), 1000],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_lesson_mark', 1018)->execute();

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
            [1000, '1000,1001,1002', 1, 'Текущая работа', 'ТР', 0, 1, 1000, time(), 1000, time(), 1000],
            [1001, '1001', 3, 'Итоговый просмотр (для выпускников)', 'ИП', 1, 1, 1001, time(), 1000, time(), 1000],
            [1002, '1000,1001,1002', 1, 'Контрольный урок', 'КУ', 0, 1, 1002, time(), 1000, time(), 1000],
            [1003, '1001', 1, 'Промежуточный просмотр', 'ПП', 0, 1, 1003, time(), 1000, time(), 1000],
            [1004, '1001', 1, 'Домашнее задание', 'ДЗ', 0, 1, 1004, time(), 1000, time(), 1000],
            [1005, '1001', 1, 'Летняя работа', 'ЛР', 0, 1, 1005, time(), 1000, time(), 1000],
            [1006, '1001', 2, 'Экзаменационный просмотр', 'ЭП', 1, 1, 1006, time(), 1000, time(), 1000],
            [1007, '1001', 1, 'Реферат', 'Реф', 0, 1, 1007, time(), 1000, time(), 1000],
            [1008, '1002', 2, 'Экзамен', 'Экз.', 1, 1, 1008, time(), 1000, time(), 1000],
            [1009, '1000,1001,1002', 1, 'Контрольный урок/Зачет', 'КЗ', 0, 1, 1009, time(), 1000, time(), 1000],
            [1010, '1000,1001,1002', 2, 'Промежуточная аттестация', 'ПА', 1, 1, 1010, time(), 1000, time(), 1000],
            [1011, '1000,1001,1002', 3, 'Итоговая аттестация', 'ИА', 1, 1, 1011, time(), 1000, time(), 1000],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_lesson_test', 1012)->execute();

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

        $this->addCommentOnTable('lesson_items', 'Уроки дисциплин индивидуального плана');
        $this->addForeignKey('lesson_items_ibfk_1', 'lesson_items', 'lesson_test_id', 'guide_lesson_test', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_items_ibfk_2', 'lesson_items', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_items_ibfk_3', 'lesson_items', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('lesson_progress', [
            'id' => $this->primaryKey(),
            'lesson_items_id' => $this->integer(),
            'studyplan_subject_id' => $this->integer(),
            'lesson_mark_id' => $this->integer()->notNull(),
            'mark_rem' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('lesson_progress_ibfk_1', 'lesson_progress', 'lesson_items_id', 'lesson_items', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('lesson_progress_ibfk_2', 'lesson_progress', 'studyplan_subject_id', 'studyplan_subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_progress_ibfk_3', 'lesson_progress', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_progress_ibfk_4', 'lesson_progress', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('lesson_progress_ibfk_5', 'lesson_progress', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('lesson_progress_view', '
(select teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
 				0 as subject_sect_id,
 				studyplan.plan_year as plan_year,
				studyplan_subject.id as studyplan_subject_id,
       			studyplan.id as studyplan_id,
       			studyplan.student_id as student_id,
 				teachers_load.id as teachers_load_id,
 				teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id
             from studyplan_subject 
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
			 inner join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id and teachers_load.subject_sect_studyplan_id = 0) 
				 )
UNION ALL 
(select subject_sect_studyplan.id as subject_sect_studyplan_id,
 				subject_sect.id as subject_sect_id,
 				subject_sect.plan_year as plan_year,
  				studyplan_subject.id as studyplan_subject_id,
			    studyplan.id as studyplan_id,
			    studyplan.student_id as student_id,
                teachers_load.id as teachers_load_id,
 				teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id
             from subject_sect_studyplan 
             inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
             inner join studyplan_subject on (studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
			 inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id  and teachers_load.studyplan_subject_id = 0) 			
				
				 )
ORDER BY subject_sect_studyplan_id, direction_id, teachers_id, studyplan_subject_id
        ')->execute();

        $this->db->createCommand()->createView('lesson_items_progress_view', '
(select teachers_load.id as teachers_load_id,
                teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                studyplan_subject.id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
                studyplan.plan_year as plan_year,
                0 as subject_sect_id,
                studyplan.id as studyplan_id,
                studyplan.student_id as student_id,
                lesson_items.id as lesson_items_id,
                lesson_items.lesson_date,
                lesson_items.lesson_topic,
                lesson_items.lesson_rem,	   
                lesson_progress.id as lesson_progress_id,
                lesson_progress.lesson_mark_id,
                guide_lesson_test.test_category,
                guide_lesson_test.test_name,
                guide_lesson_test.test_name_short,
                guide_lesson_test.plan_flag,
                guide_lesson_mark.mark_category,
                guide_lesson_mark.mark_label,
                guide_lesson_mark.mark_hint,
                guide_lesson_mark.mark_value,
                lesson_progress.mark_rem
            from lesson_items            
            left join lesson_progress  on (lesson_progress.lesson_items_id = lesson_items.id) 
            left join studyplan_subject on (studyplan_subject.id = lesson_progress.studyplan_subject_id)
            inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
            left join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id)
            left join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id) 
            inner join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id  and teachers_load.subject_sect_studyplan_id = 0) 			
				 )
UNION ALL 
(select teachers_load.id as teachers_load_id,
                teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                studyplan_subject.id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
                subject_sect.plan_year as plan_year,
                subject_sect.id as subject_sect_id,
                studyplan.id as studyplan_id,
                studyplan.student_id as student_id,
                lesson_items.id as lesson_items_id,
                lesson_items.lesson_date,
                lesson_items.lesson_topic,
                lesson_items.lesson_rem,	   
                lesson_progress.id as lesson_progress_id,
                lesson_progress.lesson_mark_id,
                guide_lesson_test.test_category,
                guide_lesson_test.test_name,
                guide_lesson_test.test_name_short,
                guide_lesson_test.plan_flag,
                guide_lesson_mark.mark_category,
                guide_lesson_mark.mark_label,
                guide_lesson_mark.mark_hint,
                guide_lesson_mark.mark_value,
                lesson_progress.mark_rem
             from lesson_items
             inner join subject_sect_studyplan  on (subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id)
             inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
             left join lesson_progress  on (lesson_progress.lesson_items_id = lesson_items.id) 
             left join studyplan_subject on (studyplan_subject.id = lesson_progress.studyplan_subject_id)
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
             left join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id)
             left join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id) 
             inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id  and teachers_load.studyplan_subject_id = 0) 			
				 )
ORDER BY direction_id, teachers_id, studyplan_subject_id, lesson_date
        ')->execute();
    }

    public function down()
    {
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
