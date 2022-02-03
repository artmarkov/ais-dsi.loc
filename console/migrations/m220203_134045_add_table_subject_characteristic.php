<?php


class m220203_134045_add_table_subject_characteristic extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('subject_characteristic', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'studyplan_subject_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_characteristic', 'Характеристика по предметам');
        $this->db->createCommand()->resetSequence('subject_characteristic', 10000)->execute();
        $this->addForeignKey('subject_characteristic_ibfk_1', 'subject_characteristic', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');

        $this->db->createCommand()->createView('subject_characteristic_view', '
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
                         subject_characteristic.id as subject_characteristic_id,
                         subject_characteristic.description as description
                 from studyplan
                 inner join studyplan_subject on (studyplan.id = studyplan_subject.studyplan_id)
                 inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
                 left join subject_characteristic on (subject_characteristic.studyplan_subject_id = studyplan_subject.id)
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
                         subject_characteristic.id as subject_characteristic_id,
                         subject_characteristic.description as description
                 from studyplan
                 inner join studyplan_subject on (studyplan_subject.studyplan_id = studyplan.id)
                 left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                                           and subject_sect.subject_id = studyplan_subject.subject_id
                                           and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
                 left join subject_characteristic on (subject_characteristic.studyplan_subject_id = studyplan_subject.id)
           )
           ORDER BY studyplan_id, subject_cat_id, subject_id
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('subject_characteristic_view')->execute();
        $this->dropTableWithHistory('subject_characteristic');
    }
}
