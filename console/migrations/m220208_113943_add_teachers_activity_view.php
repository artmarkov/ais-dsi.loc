<?php


class m220208_113943_add_teachers_activity_view extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('teachers_activity_view', '
      SELECT teachers_activity.id AS teachers_activity_id,
            teachers_activity.direction_id,
            teachers_activity.direction_vid_id,
            teachers_activity.stake_id,
            teachers.id AS teachers_id,
            teachers.position_id,
            teachers.department_list,
            teachers.tab_num,
            teachers.year_serv,
            teachers.year_serv_spec,
            user_common.id AS user_common_id,
            user_common.last_name,
            user_common.first_name,
            user_common.middle_name,
            user_common.status AS user_common_status,
            guide_teachers_direction.name AS direction_name,
            guide_teachers_direction.slug AS direction_slug,
            guide_teachers_direction_vid.name AS direction_vid_name,
            guide_teachers_direction_vid.slug AS direction_vid_slug,
            guide_teachers_level.name AS level_name,
            guide_teachers_level.slug AS level_slug,
            guide_teachers_work.name AS work_name,
            guide_teachers_work.slug AS work_slug,
            guide_teachers_stake.name AS stake_name,
            guide_teachers_stake.slug AS stake_slug,
            teachers_cost.stake_value,
            concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS fullname,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. (\', guide_teachers_direction.slug, \' - \', guide_teachers_direction_vid.slug, \')\') AS teachers_activity_memo
           FROM teachers_activity
             JOIN teachers ON teachers.id = teachers_activity.teachers_id
             JOIN user_common ON user_common.id = teachers.user_common_id
             JOIN guide_teachers_direction ON guide_teachers_direction.id = teachers_activity.direction_id
             JOIN guide_teachers_direction_vid ON guide_teachers_direction_vid.id = teachers_activity.direction_vid_id
             LEFT JOIN guide_teachers_stake ON guide_teachers_stake.id = teachers_activity.stake_id
             LEFT JOIN guide_teachers_level ON guide_teachers_level.id = teachers.level_id
             LEFT JOIN guide_teachers_work ON guide_teachers_work.id = teachers.work_id
             LEFT JOIN teachers_cost ON teachers_cost.direction_id = teachers_activity.direction_id AND teachers_cost.stake_id = teachers_activity.stake_id
          ORDER BY teachers.id;
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_activity_memo', 'teachers_activity_view', 'teachers_activity_id', 'teachers_activity_memo', 'fullname', 'user_common_status', null, 'Список активностей преподавателей'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_activity_memo'])->execute();
        $this->db->createCommand()->dropView('teachers_activity_view')->execute();
    }
}
