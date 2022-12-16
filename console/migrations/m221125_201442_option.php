<?php

use \artsoft\db\BaseMigration;

class m221125_201442_option extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;
//        $this->createTableWithHistory('subject_sect', [
//            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
//            'union_id' => $this->integer()->notNull(),
//            'subject_cat_id' => $this->integer()->notNull(),
//            'subject_id' => $this->integer()->notNull(),
//            'subject_vid_id' => $this->integer()->notNull(),
//            'subject_type_id' => $this->integer(),
//            'sect_name' => $this->string(127)->comment('Название группы'),
//            'course_list' => $this->string(1024)->comment('Список курсов'),
//            'course_flag' => $this->integer()->notNull()->comment('Распределить по курсам(Да/Нет)'),
//            'sub_group_qty' => $this->integer()->notNull()->comment('Кол-во подгрупп в группе'),
//            'created_at' => $this->integer()->notNull(),
//            'created_by' => $this->integer(),
//            'updated_at' => $this->integer()->notNull(),
//            'updated_by' => $this->integer(),
//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
//            'version' => $this->bigInteger()->notNull()->defaultValue(0),
//        ], $tableOptions);
//
//        $this->addCommentOnTable('subject_sect', 'Учебные группы');
//        $this->db->createCommand()->resetSequence('subject_sect', 10000)->execute();
//        $this->createIndex('subject_cat_id', 'subject_sect', 'subject_cat_id');
//        $this->createIndex('subject_id', 'subject_sect', 'subject_id');
//        $this->createIndex('subject_type_id', 'subject_sect', 'subject_type_id');
//        $this->createIndex('subject_vid_id', 'subject_sect', 'subject_vid_id');
//        $this->addForeignKey('subject_sect_ibfk_1', 'subject_sect', 'union_id', 'education_union', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('subject_sect_ibfk_2', 'subject_sect', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('subject_sect_ibfk_3', 'subject_sect', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('subject_sect_ibfk_4', 'subject_sect', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('subject_sect_ibfk_5', 'subject_sect', 'subject_vid_id', 'guide_subject_vid', 'id', 'NO ACTION', 'NO ACTION');
//
//        $this->createTableWithHistory('subject_sect_studyplan', [
//            'id' => $this->primaryKey(),
//            'subject_sect_id' => $this->integer(),
//            'subject_type_id' => $this->integer()->notNull(),
//            'plan_year' => $this->integer()->notNull(),
//            'group_num' => $this->integer()->notNull(),
//            'course' => $this->integer(),
//            'studyplan_subject_list' => $this->text(),
//            'created_at' => $this->integer()->notNull(),
//            'created_by' => $this->integer(),
//            'updated_at' => $this->integer()->notNull(),
//            'updated_by' => $this->integer(),
//            'version' => $this->bigInteger()->notNull()->defaultValue(0),
//        ], $tableOptions);
//
//        $this->addCommentOnTable('subject_sect_studyplan', 'Распределение по учебным группам');
//
//        $this->createIndex('subject_sect_id', 'subject_sect_studyplan', 'subject_sect_id');
//        $this->createIndex('plan_year', 'subject_sect_studyplan', 'plan_year');
//        $this->createIndex('course', 'subject_sect_studyplan', 'course');
//
//        $this->addForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan', 'subject_sect_id', 'subject_sect', 'id', 'CASCADE', 'CASCADE');
//        $this->addForeignKey('subject_sect_studyplan_ibfk_2', 'subject_sect_studyplan', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
//
//        $this->db->createCommand()->createView('teachers_load_studyplan_view', '
// SELECT studyplan_subject.id AS studyplan_subject_id,
//    studyplan_subject.week_time,
//    studyplan_subject.year_time_consult,
//    0 AS subject_sect_studyplan_id,
//    studyplan_subject.id::text AS studyplan_subject_list,
//    0 AS subject_sect_id,
//    studyplan.id AS studyplan_id,
//    studyplan.student_id,
//    studyplan.plan_year,
//    studyplan.status,
//    teachers_load.id AS teachers_load_id,
//    teachers_load.direction_id,
//    teachers_load.teachers_id,
//    teachers_load.load_time,
//    teachers_load.load_time_consult,
//	guide_subject_category.sort_order,
//	studyplan_subject.subject_vid_id
//   FROM studyplan
//     JOIN studyplan_subject ON studyplan_subject.studyplan_id = studyplan.id
//	 JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
//     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
//     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
//UNION ALL
// SELECT studyplan_subject.id AS studyplan_subject_id,
//    studyplan_subject.week_time,
//    studyplan_subject.year_time_consult,
//    subject_sect_studyplan.id AS subject_sect_studyplan_id,
//    subject_sect_studyplan.studyplan_subject_list,
//    subject_sect.id AS subject_sect_id,
//    studyplan.id AS studyplan_id,
//    studyplan.student_id,
//    studyplan.plan_year,
//    studyplan.status,
//    teachers_load.id AS teachers_load_id,
//    teachers_load.direction_id,
//    teachers_load.teachers_id,
//    teachers_load.load_time,
//    teachers_load.load_time_consult,
//	guide_subject_category.sort_order,
//	studyplan_subject.subject_vid_id
//   FROM studyplan
//     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
//	 JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
//     JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
//     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
//     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
//UNION ALL
// SELECT studyplan_subject.id AS studyplan_subject_id,
//    studyplan_subject.week_time,
//    studyplan_subject.year_time_consult,
//    NULL::integer AS subject_sect_studyplan_id,
//    NULL::text AS studyplan_subject_list,
//    NULL::integer AS subject_sect_id,
//    studyplan.id AS studyplan_id,
//    studyplan.student_id,
//    studyplan.plan_year,
//    studyplan.status,
//    NULL::integer AS teachers_load_id,
//    NULL::integer AS direction_id,
//    NULL::integer AS teachers_id,
//    NULL::double precision AS load_time,
//    NULL::double precision AS load_time_consult,
//	guide_subject_category.sort_order,
//	studyplan_subject.subject_vid_id
//   FROM studyplan_subject
//   JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
//     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
//  WHERE NOT (studyplan_subject.id IN ( SELECT studyplan_subject_1.id
//           FROM studyplan studyplan_1
//             JOIN studyplan_subject studyplan_subject_1 ON studyplan_subject_1.studyplan_id = studyplan_1.id
//             JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject_1.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
//             LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject_1.id AND teachers_load.subject_sect_studyplan_id = 0
//        UNION ALL
//         SELECT studyplan_subject_1.id
//           FROM studyplan studyplan_1
//             JOIN studyplan_subject studyplan_subject_1 ON studyplan_1.id = studyplan_subject_1.studyplan_id
//             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject_1.subject_cat_id AND subject_sect.subject_id = studyplan_subject_1.subject_id AND subject_sect.subject_vid_id = studyplan_subject_1.subject_vid_id
//             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject_1.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
//             JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0))
//   ORDER BY  sort_order, subject_vid_id
//
//        ')->execute();
//
//        $this->db->createCommand()->createView('teachers_load_view', '
//         (select teachers_load.studyplan_subject_id as studyplan_subject_id,
//                 0 as subject_sect_studyplan_id,
//                 studyplan_subject.id::text as studyplan_subject_list,
//                 0 as subject_sect_id,
//                 studyplan.plan_year as plan_year,
//                 studyplan_subject.week_time as week_time,
//                 studyplan_subject.year_time_consult as year_time_consult,
//                 teachers_load.id as teachers_load_id,
//                 teachers_load.direction_id as direction_id,
//                 teachers_load.teachers_id as teachers_id,
//                 teachers_load.load_time as load_time,
//			     teachers_load.load_time_consult as load_time_consult
//             from studyplan_subject
//			 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
//			 left join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id
//			 and teachers_load.subject_sect_studyplan_id = 0)
//           )
//UNION ALL
//         (select 0 as studyplan_subject_id,
//                 subject_sect_studyplan.id as subject_sect_studyplan_id,
//                 subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
//                 subject_sect.id as subject_sect_id,
//                 subject_sect_studyplan.plan_year as plan_year,
//                 (select MAX(week_time)
//					 from studyplan_subject
//					 where studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])
//				 ) as week_time,
//				 (select MAX(year_time_consult)
//					 from studyplan_subject
//					 where studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])
//				 ) as year_time_consult,
//                 teachers_load.id as teachers_load_id,
//                 teachers_load.direction_id as direction_id,
//                 teachers_load.teachers_id as teachers_id,
//                 teachers_load.load_time as load_time,
//			     teachers_load.load_time_consult as load_time_consult
//             from subject_sect_studyplan
//			 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
//			 left join teachers_load on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id
//			  and teachers_load.studyplan_subject_id = 0)
//			  )
//ORDER BY subject_sect_studyplan_id, studyplan_subject_id, direction_id, teachers_id
//        ')->execute();
//

//        $this->db->createCommand()->createView('subject_sect_view', '
//         select subject_sect_studyplan.id as id,
//                subject.id as subject_id,
//                subject_sect.subject_cat_id,
//                subject_sect.subject_type_id,
//                subject_sect.subject_vid_id,
//                subject_sect.sect_name,
//                subject_sect.course_list,
//                subject_sect.course_flag,
//                subject_sect.sub_group_qty,
//                subject_sect_studyplan.course,
//                subject_sect_studyplan.plan_year,
//                subject_sect_studyplan.group_num,
//    	        concat(subject.name, \'(\',guide_subject_vid.slug,\') \') as sect_memo_1,
//                concat(subject.name, \'(\',guide_subject_category.slug, \' \',guide_subject_vid.slug,\')\') as sect_memo_2,
//		        concat(education_union.class_index, \' \', subject_sect.sect_name, \' (\',subject.name, \'-\',guide_subject_category.slug, \') \') as sect_name_1,
//		        concat(education_union.class_index, \' \', subject_sect.sect_name, \' (\',subject.name, \'-\',guide_subject_category.slug, \') \',\' \',guide_subject_vid.slug,\' \', guide_subject_type.slug) as sect_name_2,
//			    concat(education_union.class_index, \' \', subject_sect.sect_name, \' (\',guide_subject_type.slug, \') \') as sect_name_3
//         from subject_sect_studyplan
//         inner join subject_sect on subject_sect.id = subject_sect_studyplan.subject_sect_id
//         inner join guide_subject_category on guide_subject_category.id = subject_sect.subject_cat_id
//         inner join subject on subject.id = subject_sect.subject_id
//		 inner join guide_subject_vid on guide_subject_vid.id = subject_sect.subject_vid_id
//		 inner join guide_subject_type on guide_subject_type.id = subject_sect_studyplan.subject_type_id
//         inner join education_union on education_union.id = subject_sect.union_id
//        ')->execute();

        $this->db->createCommand()->createView('studyplan_subject_view', '
          (SELECT studyplan_subject.id AS studyplan_subject_id,
                0 AS subject_sect_studyplan_id,
                studyplan_subject.studyplan_id,
                studyplan_subject.week_time,
                studyplan_subject.year_time,
                studyplan_subject.cost_month_summ,
                studyplan.student_id,
                studyplan.course,
                studyplan.plan_year,
                guide_subject_category.name AS subject_category_name,
                guide_subject_category.slug AS subject_category_slug,
                subject.id AS subject_id,
                subject.name AS subject_name,
                subject.slug AS subject_slug,
                guide_subject_vid.name AS subject_vid_name,
                guide_subject_vid.slug AS subject_vid_slug,
                guide_subject_type.name AS subject_type_name,
                guide_subject_type.slug AS subject_type_slug,
                education_programm.name AS education_programm_name,
                education_programm.short_name AS education_programm_short_name,
                guide_education_cat.name AS education_cat_name,
                guide_education_cat.short_name AS education_cat_short_name,
                user_common.status,
                students.position_id,
                concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
                concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
                concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
                concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
                concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_4
               FROM studyplan_subject
               JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
                 JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
                 JOIN students ON students.id = studyplan.student_id
                 JOIN user_common ON user_common.id = students.user_common_id
                 JOIN education_programm ON education_programm.id = studyplan.programm_id
                 JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
                 JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                 JOIN subject ON subject.id = studyplan_subject.subject_id
                 JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id)
UNION ALL
             (SELECT studyplan_subject.id AS studyplan_subject_id,
                subject_sect_studyplan.id AS subject_sect_studyplan_id,
                studyplan_subject.studyplan_id,
                studyplan_subject.week_time,
                studyplan_subject.year_time,
                studyplan_subject.cost_month_summ,
                studyplan.student_id,
                studyplan.course,
                studyplan.plan_year,
                guide_subject_category.name AS subject_category_name,
                guide_subject_category.slug AS subject_category_slug,
                subject.id AS subject_id,
                subject.name AS subject_name,
                subject.slug AS subject_slug,
                guide_subject_vid.name AS subject_vid_name,
                guide_subject_vid.slug AS subject_vid_slug,
                guide_subject_type.name AS subject_type_name,
                guide_subject_type.slug AS subject_type_slug,
                education_programm.name AS education_programm_name,
                education_programm.short_name AS education_programm_short_name,
                guide_education_cat.name AS education_cat_name,
                guide_education_cat.short_name AS education_cat_short_name,
                user_common.status,
                students.position_id,
                concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
                concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
                concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
                concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
                concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_4
               FROM studyplan_subject
               JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
               LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                 JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                 JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
                 JOIN students ON students.id = studyplan.student_id
                 JOIN user_common ON user_common.id = students.user_common_id
                 JOIN education_programm ON education_programm.id = studyplan.programm_id
                 JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
                 JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                 JOIN subject ON subject.id = studyplan_subject.subject_id
                 JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id)
  ORDER BY 13;
        ')->execute();
    }

        public function down()
    {

    }
}
