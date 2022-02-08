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
            user_common.id as user_common_id,
            user_common.last_name,
            user_common.first_name,
            user_common.middle_name,
            user_common.status as  user_common_status,
            guide_teachers_direction.name as direction_name,
            guide_teachers_direction.slug as direction_slug,
            guide_teachers_direction_vid.name as direction_vid_name,
            guide_teachers_direction_vid.slug as direction_vid_slug,
            guide_teachers_stake.name as stake_name,
            guide_teachers_stake.slug as stake_slug,
            teachers_cost.stake_value as stake_value,	
            concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS fullname,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. (\', guide_teachers_direction.slug, \' - \', guide_teachers_direction_vid.slug, \')\') AS teachers_activity_memo
            FROM teachers_activity
            inner join teachers on teachers.id = teachers_activity.teachers_id
            inner join user_common on user_common.id = teachers.user_common_id
            inner join guide_teachers_direction on guide_teachers_direction.id = teachers_activity.direction_id
            inner join guide_teachers_direction_vid on guide_teachers_direction_vid.id = teachers_activity.direction_vid_id
            left join guide_teachers_stake on guide_teachers_stake.id = teachers_activity.stake_id
            left join teachers_cost on (teachers_cost.direction_id = teachers_activity.direction_id and teachers_cost.stake_id = teachers_activity.stake_id)
        ORDER BY teachers_id
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_activity_memo', 'teachers_activity_view', 'teachers_activity_id', 'teachers_activity_memo', 'teachers_id', 'user_common_status', null, 'Список активностей преподавателей'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_activity_memo'])->execute();
        $this->db->createCommand()->dropView('teachers_activity_view')->execute();
    }
}
