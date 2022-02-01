<?php


class m211119_191543_add_table_teachers_plan extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('teachers_load', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'load_time' => $this->float()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_load', 'Нагрузка преподавателя');
        $this->db->createCommand()->resetSequence('teachers_load', 10000)->execute();
        $this->createIndex('subject_sect_studyplan_id', 'teachers_load', 'subject_sect_studyplan_id');
        $this->createIndex('teachers_id', 'teachers_load', 'teachers_id');
        $this->addForeignKey('teachers_load_ibfk_1', 'teachers_load', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_2', 'teachers_load', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('teachers_load_sect_view', '
         select subject_sect_studyplan.id as subject_sect_studyplan_id,	
                subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
                subject_sect_studyplan.subject_type_id as subject_type_id,	
                subject_sect_studyplan.class_name as class_name,	
                subject_sect.id as subject_sect_id,
				subject_sect.plan_year as plan_year,	
                subject_sect.subject_cat_id as subject_cat_id,
			    subject_sect.subject_id as subject_id,
			    subject_sect.subject_vid_id as subject_vid_id,
                teachers_load.id as teachers_load_id,
				teachers_load.studyplan_subject_id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
                teachers_load.load_time as load_time
         from subject_sect_studyplan
		 inner join subject_sect on subject_sect.id = subject_sect_studyplan.subject_sect_id
		 left join teachers_load  on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
		                            and teachers_load.studyplan_subject_id = 0)
         order by subject_sect_id, subject_sect_studyplan_id, direction_id
        ')->execute();

 $this->db->createCommand()->createView('teachers_load_teachers_view', '
        (select teachers_load.id as teachers_load_id,
				teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
				teachers_load.studyplan_subject_id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
                teachers_load.load_time as load_time,
				studyplan.id::text as studyplan_subject_list,
				studyplan.course as course,
			    studyplan_subject.subject_cat_id as subject_cat_id,
			    studyplan_subject.subject_id as subject_id,
			    studyplan_subject.subject_type_id as subject_type_id,
			    studyplan_subject.subject_vid_id as subject_vid_id,
				studyplan.plan_year as plan_year	
                 from teachers_load
				 inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id and teachers_load.subject_sect_studyplan_id = 0)
				 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
				 )
UNION ALL 
		(select teachers_load.id as teachers_load_id,
  				teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
  				teachers_load.studyplan_subject_id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
                teachers_load.load_time as load_time,
				subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
				subject_sect.course as course,
			    subject_sect.subject_cat_id as subject_cat_id,
			    subject_sect.subject_id as subject_id,
			    subject_sect.subject_type_id as subject_type_id,
			    subject_sect.subject_vid_id as subject_vid_id,
				subject_sect.plan_year as plan_year	
                 from teachers_load
				 inner join subject_sect_studyplan on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id and teachers_load.studyplan_subject_id = 0)
				 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
				 )
ORDER BY direction_id, teachers_id
        ')->execute();

        $this->db->createCommand()->createView('teachers_load_view', '
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
                         studyplan_subject.week_time as week_time,
                         studyplan_subject.year_time as year_time,
                         teachers_load.id as teachers_load_id,
                         teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         teachers_load.direction_id as direction_id,
                         teachers_load.teachers_id as teachers_id,
                         teachers_load.load_time as load_time
                 from studyplan
                 inner join studyplan_subject on (studyplan.id = studyplan_subject.studyplan_id)
                 inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
                 left join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
											and teachers_load.subject_sect_studyplan_id = 0)
                 
           )
           UNION
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
                         studyplan_subject.week_time as week_time,
                         studyplan_subject.year_time as year_time,
                         teachers_load.id as teachers_load_id,
                         teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         teachers_load.direction_id as direction_id,
                         teachers_load.teachers_id as teachers_id,
                         teachers_load.load_time as load_time
                 from studyplan
                 inner join studyplan_subject on (studyplan_subject.studyplan_id = studyplan.id)
                 left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                                           and subject_sect.subject_id = studyplan_subject.subject_id
                                           and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
                 left join teachers_load  on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
		                            and teachers_load.studyplan_subject_id = 0)
           )
           ORDER BY studyplan_id, subject_cat_id, subject_sect_studyplan_id, direction_id
  		   
        ')->execute();

        $this->createTableWithHistory('subject_schedule', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'teachers_load_id' => $this->integer(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_in' => $this->integer(),
            'time_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_schedule', 'Расписание занятий');
        $this->db->createCommand()->resetSequence('subject_schedule', 10000)->execute();
        $this->addForeignKey('subject_schedule_ibfk_1', 'subject_schedule', 'teachers_load_id', 'teachers_load', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('teachers_plan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_plan_in' => $this->integer(),
            'time_plan_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_plan', 'Планирование инд. занятий преподавателя');
        $this->db->createCommand()->resetSequence('teachers_plan', 10000)->execute();
        $this->addForeignKey('teachers_plan_ibfk_1', 'teachers_plan', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_plan_ibfk_2', 'teachers_plan', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {

        $this->dropForeignKey('teachers_plan_ibfk_1', 'teachers_plan');
        $this->dropForeignKey('teachers_plan_ibfk_2', 'teachers_plan');
        $this->dropForeignKey('subject_schedule_ibfk_1', 'subject_schedule');
        $this->db->createCommand()->dropView('teachers_load_teachers_view')->execute();
        $this->db->createCommand()->dropView('teachers_load_sect_view')->execute();
        $this->db->createCommand()->dropView('teachers_load_view')->execute();
        $this->dropForeignKey('teachers_load_ibfk_1', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_2', 'teachers_load');

        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('subject_schedule');
        $this->dropTableWithHistory('teachers_load');

    }
}
