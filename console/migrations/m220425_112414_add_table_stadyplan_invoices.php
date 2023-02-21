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

        $this->addForeignKey('studyplan_invoices_ibfk_1', 'studyplan_invoices', 'studyplan_id', 'studyplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('studyplan_invoices_ibfk_2', 'studyplan_invoices', 'invoices_id', 'invoices', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_ibfk_3', 'studyplan_invoices', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_ibfk_4', 'studyplan_invoices', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_ibfk_5', 'studyplan_invoices', 'type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_ibfk_6', 'studyplan_invoices', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_invoices_ibfk_7', 'studyplan_invoices', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

// Учитывает нагрузку преподавателей и наличие расписания занятий, дисциплины должны быть в активном статусе. Статус активности плана задать в контроллере.
// Для фильтрации заданы все массивы
        $this->db->createCommand()->createView('studyplan_invoices_view', '
 SELECT a.studyplan_id,
    a.programm_id,
    a.student_id,
    a.student_fio,
    a.plan_year,
    a.course,
    a.status,
    a.education_cat_id,
    a.programm_short_name,
    a.education_cat_short_name,
    a.studyplan_subjects,
    a.subject_list,
    a.subject_type_list,
    a.subject_type_sect_list,
    a.subject_vid_list,
    a.direction_list,
    a.teachers_list,
    a.studyplan_invoices_id,
    a.invoices_id,
    a.studyplan_invoices_status,
    a.month_time_fact,
    a.invoices_summ,
    a.invoices_date,
    a.payment_time,
    a.payment_time_fact
   FROM ( SELECT studyplan.id AS studyplan_id,
            studyplan.programm_id,
            studyplan.student_id,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
            studyplan.plan_year,
            studyplan.course,
            studyplan.status,
            education_programm.education_cat_id,
            education_programm.short_name AS programm_short_name,
            guide_education_cat.short_name AS education_cat_short_name,
            array_to_string(ARRAY( SELECT DISTINCT t.subject
                   FROM ( SELECT concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS subject
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                             JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
                             JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                             JOIN subject ON subject.id = studyplan_subject.subject_id
                             JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \') - \', guide_subject_vid.slug, \' \', studyplan_subject.week_time * 4::double precision, \' час/мес\') AS subject
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                             JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
                             JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                             JOIN subject ON subject.id = studyplan_subject.subject_id
                             JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS studyplan_subjects,
            array_to_string(ARRAY( SELECT DISTINCT t.subject_id
                   FROM ( SELECT studyplan_subject.subject_id
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT studyplan_subject.subject_id
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS subject_list,
            array_to_string(ARRAY( SELECT DISTINCT t.subject_type_id
                   FROM ( SELECT studyplan_subject.subject_type_id
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT studyplan_subject.subject_type_id
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS subject_type_list,
            array_to_string(ARRAY( SELECT DISTINCT subject_sect_studyplan.subject_type_id
                   FROM studyplan_subject
                     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                  WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0), \',\'::text) AS subject_type_sect_list,
            array_to_string(ARRAY( SELECT DISTINCT t.subject_vid_id
                   FROM ( SELECT studyplan_subject.subject_vid_id
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT studyplan_subject.subject_vid_id
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS subject_vid_list,
            array_to_string(ARRAY( SELECT DISTINCT t.direction_id
                   FROM ( SELECT teachers_load.direction_id
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT teachers_load.direction_id
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS direction_list,
            array_to_string(ARRAY( SELECT DISTINCT t.teachers_id
                   FROM ( SELECT teachers_load.teachers_id
                           FROM teachers_load
                             JOIN studyplan_subject ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0
                        UNION ALL
                         SELECT teachers_load.teachers_id
                           FROM studyplan_subject
                             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                             JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
                          WHERE studyplan_subject.studyplan_id = studyplan.id AND studyplan_subject.status <> 0) t), \',\'::text) AS teachers_list,
            studyplan_invoices.id AS studyplan_invoices_id,
            studyplan_invoices.invoices_id,
            studyplan_invoices.status AS studyplan_invoices_status,
            studyplan_invoices.month_time_fact,
            studyplan_invoices.invoices_summ,
            studyplan_invoices.invoices_date,
            studyplan_invoices.payment_time,
            studyplan_invoices.payment_time_fact
           FROM studyplan
             JOIN education_programm ON education_programm.id = studyplan.programm_id
             JOIN guide_education_cat ON education_programm.education_cat_id = guide_education_cat.id
             LEFT JOIN studyplan_invoices ON studyplan_invoices.studyplan_id = studyplan.id
             JOIN students ON students.id = studyplan.student_id
             JOIN user_common ON user_common.id = students.user_common_id) a
  WHERE a.teachers_list <> \'\'::text AND a.studyplan_subjects <> \'\'::text
  ORDER BY a.studyplan_id;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('studyplan_invoices_view')->execute();
        $this->dropTableWithHistory('studyplan_invoices');

    }
}
