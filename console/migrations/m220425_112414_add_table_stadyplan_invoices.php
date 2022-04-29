<?php


class m220425_112414_add_table_stadyplan_invoices extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('studyplan_invoices', [
            'id' => $this->primaryKey(),
            'studyplan_id' => $this->integer()->notNull()->comment('Учебный план'),
            'invoices_id' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Вид платежа'),
            'direction_id' => $this->integer(),
            'teachers_id' => $this->integer(),
            'type_id' => $this->integer()->comment('Тип платежа(бюджет, внебюджет)'),
            'vid_id' => $this->integer()->comment('Вид платежа(индивидуальные, групповые)'),
            'month_time_fact' => $this->integer()->comment('Фактически оплаченные часы'),
            'invoices_tabel_flag' => $this->integer()->comment('Учесть в табеле фактически оплаченные часы'),
            'invoices_date' => $this->integer()->comment('Дата платежа'),
            'invoices_summ' => $this->float()->comment('Сумма платежа'),
            'payment_time' => $this->integer()->comment('Время выполнения платежя'),
            'payment_time_fact' => $this->integer()->comment('Время поступления денег на счет'),
            'invoices_app' => $this->string(256)->comment('Назначение платежа'),
            'invoices_rem' => $this->string(512)->comment('Примечание к платежу'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Статус платежа(В работе,Оплачено,Задолженность по оплате)'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('studyplan_invoices_1', 'studyplan_invoices', 'studyplan_id', 'studyplan', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_2', 'studyplan_invoices', 'invoices_id', 'invoices', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_3', 'studyplan_invoices', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_4', 'studyplan_invoices', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_5', 'studyplan_invoices', 'type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_6', 'studyplan_invoices', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_7', 'studyplan_invoices', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

// Учитывает нагрузку преподавателей и наличие расписания занятий, дисциплины должны быть в активном статусе. Статус активности плана задать в контроллере.
// Для фильтрации заданы все массивы
        $this->db->createCommand()->createView('studyplan_invoices_view', '
select * from 
	(select studyplan.id as studyplan_id,
				 studyplan.programm_id as programm_id,
				 studyplan.student_id as student_id,
				 studyplan.plan_year as plan_year,
				 studyplan.course as course,
				 studyplan.status as status,
				 education_programm.education_cat_id as education_cat_id,
				 array_to_string(ARRAY(select DISTINCT t.id from (
											(select studyplan_subject.id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select studyplan_subject.id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as studyplan_subject_ids,					   
				  array_to_string(ARRAY(select DISTINCT t.subject_id from (
											(select studyplan_subject.subject_id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select studyplan_subject.subject_id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as subject_list,
				  array_to_string(ARRAY(select DISTINCT t.subject_type_id from (
											(select studyplan_subject.subject_type_id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select studyplan_subject.subject_type_id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as subject_type_list,
	             array_to_string(ARRAY(select DISTINCT subject_sect_studyplan.subject_type_id from studyplan_subject 
                 						left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                                           and subject_sect.subject_id = studyplan_subject.subject_id
                                           and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 						inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 						inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   and teachers_load.studyplan_subject_id = 0) 
									   where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0), \',\')::text as subject_type_sect_list,	
				 array_to_string(ARRAY(select DISTINCT t.subject_vid_id from (
											(select studyplan_subject.subject_vid_id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select studyplan_subject.subject_vid_id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as subject_vid_list,
				  array_to_string(ARRAY(select DISTINCT t.direction_id from (
											(select teachers_load.direction_id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select teachers_load.direction_id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as direction_list,																				   
				 array_to_string(ARRAY(select DISTINCT t.teachers_id from (
											(select teachers_load.teachers_id from teachers_load 
											   inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id 
												 and teachers_load.subject_sect_studyplan_id = 0)
											   inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
												 where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0)
										 UNION ALL            
											(select teachers_load.teachers_id from studyplan_subject 
                 							  inner join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
												and subject_sect.subject_id = studyplan_subject.subject_id
												and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 							  inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 
				 							  inner join teachers_load on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
										   		and teachers_load.studyplan_subject_id = 0) 
											  inner join subject_schedule on (subject_schedule.teachers_load_id = teachers_load.id)
									   			where studyplan_subject.studyplan_id = studyplan.id and studyplan_subject.status != 0) 
																			) as t), \',\')::text as teachers_list,
				 studyplan_invoices.id as studyplan_invoices_id, 
				 studyplan_invoices.invoices_id as invoices_id,
				 studyplan_invoices.status as studyplan_invoices_status,
				 studyplan_invoices.month_time_fact as month_time_fact,
				 studyplan_invoices.invoices_summ as invoices_summ,
				 studyplan_invoices.invoices_date as invoices_date,
				 studyplan_invoices.payment_time as payment_time,
				 studyplan_invoices.payment_time_fact as payment_time_fact
from studyplan
inner join education_programm on (education_programm.id = studyplan.programm_id)
left join studyplan_invoices on (studyplan_invoices.studyplan_id = studyplan.id) 
	 ) as a 
where a.teachers_list != \'\' and a.studyplan_subject_ids != \'\' order by a.studyplan_id;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('studyplan_invoices_view')->execute();
        $this->dropTableWithHistory('studyplan_invoices');

    }
}
