<?php


class m210824_135345_ref extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('parents_dependence_view', '
         SELECT student_id, 
                parent_id,
                guide_user_relation.id as relation_id,
		        guide_user_relation.name as relation_name,
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM students 
        INNER JOIN student_dependence ON student_dependence.student_id = students.id 
		INNER JOIN guide_user_relation ON student_dependence.relation_id = guide_user_relation.id 
		INNER JOIN parents ON student_dependence.parent_id = parents.id 
		INNER JOIN user_common ON user_common.id = parents.user_common_id
		WHERE user_common.user_category=\'parents\' 
		ORDER BY user_common.last_name, user_common.first_name
       
        ')->execute();


        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['parents_dependence_relation_name', 'parents_dependence_view', 'parent_id', 'relation_name', 'relation_name', 'student_id', null, 'Родители (отношения)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'parents_dependence_relation_name'])->execute();
        $this->db->createCommand()->dropView('parents_dependence_view')->execute();

    }
}