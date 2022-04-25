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


        $this->db->createCommand()->createView('studyplan_invoices_view', '
        (select studyplan_subject.id as studyplan_subject_id,
				 studyplan_subject.subject_type_id as subject_type_id,
			     studyplan_subject.week_time as week_time,
			     studyplan.id as studyplan_id,
			     studyplan.student_id as student_id,
			     studyplan.plan_year as plan_year,
			     studyplan.status as status,
			     teachers_load.id as teachers_load_id,
			     teachers_load.direction_id as direction_id,
			     teachers_load.teachers_id as teachers_id,
			     teachers_load.load_time as load_time,
				 studyplan_invoices.id as studyplan_invoices_id,
				 studyplan_invoices.invoices_id as invoices_id,
				 studyplan_invoices.status as studyplan_invoices_status,
				 studyplan_invoices.month_time_fact as month_time_fact,
				 studyplan_invoices.invoices_summ as invoices_summ
	         from studyplan_subject
             inner join studyplan on (studyplan_subject.studyplan_id = studyplan.id)
             inner join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id
				  and teachers_load.subject_sect_studyplan_id = 0)
			 left join studyplan_invoices on (studyplan_invoices.studyplan_id = studyplan.id 
			      and studyplan_invoices.type_id = studyplan_subject.subject_type_id 
			      and studyplan_invoices.vid_id = 1)
			 )
UNION ALL 
		(select studyplan_subject.id as studyplan_subject_id,
				 studyplan_subject.subject_type_id as subject_type_id,
			     studyplan_subject.week_time as week_time,
			     studyplan.id as studyplan_id,
			     studyplan.student_id as student_id,
			     studyplan.plan_year as plan_year,
			     studyplan.status as status,
			     teachers_load.id as teachers_load_id,
			     teachers_load.direction_id as direction_id,
			     teachers_load.teachers_id as teachers_id,
			     teachers_load.load_time as load_time,
				 studyplan_invoices.id as studyplan_invoices_id,
				 studyplan_invoices.invoices_id as invoices_id,
				 studyplan_invoices.status as studyplan_invoices_status,
				 studyplan_invoices.month_time_fact as month_time_fact,
				 studyplan_invoices.invoices_summ as invoices_summ
	         from studyplan_subject
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
             left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                  and subject_sect.subject_id = studyplan_subject.subject_id
                  and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
             inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id
                  and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[]))
             inner join teachers_load  on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
                  and teachers_load.studyplan_subject_id = 0)
			 left join studyplan_invoices on (studyplan_invoices.studyplan_id = studyplan.id 
			      and studyplan_invoices.type_id = studyplan_subject.subject_type_id 
			      and studyplan_invoices.vid_id = 2)
			 )
			 ORDER BY studyplan_id, subject_type_id, studyplan_subject_id
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('studyplan_invoices_view')->execute();
        $this->dropTableWithHistory('studyplan_invoices');

    }
}
