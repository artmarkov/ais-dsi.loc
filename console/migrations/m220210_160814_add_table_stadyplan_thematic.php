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
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'thematic_category' => $this->integer()->notNull(),
            'period_in' => $this->integer()->notNull(),
            'period_out' => $this->integer()->notNull(),
            'template_flag' => $this->integer()->defaultValue(0),
            'template_name' => $this->string(256),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

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

        $this->db->createCommand()->createView('studyplan_thematic_view', '
           (select studyplan.id as studyplan_id,
                         studyplan.student_id as student_id,
                         studyplan.plan_year as plan_year,
                         studyplan.programm_id as programm_id,
                         studyplan.speciality_id as speciality_id,
                         studyplan.course as course,
                         studyplan.status as status,
                         studyplan_subject.id as studyplan_subject_id,
                         studyplan_subject.subject_cat_id as subject_cat_id,
                         studyplan_subject.subject_id as subject_id,
                         studyplan_subject.subject_type_id as subject_type_id,
                         studyplan_subject.subject_vid_id as subject_vid_id,
                         studyplan_thematic.id as studyplan_thematic_id,
                         studyplan_thematic.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         studyplan_thematic.thematic_category as thematic_category,
                         studyplan_thematic.period_in as period_in,
                         studyplan_thematic.period_out as period_out
                 from studyplan
                 inner join studyplan_subject on (studyplan.id = studyplan_subject.studyplan_id)
                 inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
                 left join studyplan_thematic on (studyplan_thematic.studyplan_subject_id = studyplan_subject.id 
											and studyplan_thematic.subject_sect_studyplan_id = 0)
           )
           UNION ALL
           (select studyplan.id as studyplan_id,
                         studyplan.student_id as student_id,
                         studyplan.plan_year as plan_year,
                         studyplan.programm_id as programm_id,
                         studyplan.speciality_id as speciality_id,
                         studyplan.course as course,
                         studyplan.status as status,
                         studyplan_subject.id as studyplan_subject_id,
                         studyplan_subject.subject_cat_id as subject_cat_id,
                         studyplan_subject.subject_id as subject_id,
                         studyplan_subject.subject_type_id as subject_type_id,
                         studyplan_subject.subject_vid_id as subject_vid_id,
                         studyplan_thematic.id as studyplan_thematic_id,
                         studyplan_thematic.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         studyplan_thematic.thematic_category as thematic_category,
                         studyplan_thematic.period_in as period_in,
                         studyplan_thematic.period_out as period_out
                 from studyplan
                 inner join studyplan_subject on (studyplan_subject.studyplan_id = studyplan.id)
                 left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                                           and subject_sect.subject_id = studyplan_subject.subject_id
                                           and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
                 left join studyplan_thematic  on (studyplan_thematic.subject_sect_studyplan_id = subject_sect_studyplan.id
		                            and studyplan_thematic.studyplan_subject_id = 0)
           )
           ORDER BY studyplan_id, subject_cat_id, subject_sect_studyplan_id
  		   
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
