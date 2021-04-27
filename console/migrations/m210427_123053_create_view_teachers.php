<?php

/**
 * Class m210427_123053_create_view_teachers
 */
class m210427_123053_create_view_teachers extends \artsoft\db\BaseMigration
{
    /**
     * @return bool|void
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->db->createCommand()->createView('teachers_view', '
         SELECT users.id AS user_id, user_common.id AS user_common_id, teachers.id AS teachers_id, users.username, users.email, users.status AS user_status, 
                user_common.status, position_id, department_list, user_common.last_name,user_common.first_name,user_common.middle_name, 
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM teachers 
        INNER JOIN user_common ON user_common.id = teachers.user_common_id 
        LEFT JOIN users ON user_common.user_id = users.id 
        WHERE user_common.user_category=\'teachers\'
        ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_fio', 'teachers_view', 'teachers_id', 'fio', 'fio', 'status', null, 'Преподаватели (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_fullname', 'teachers_view', 'teachers_id', 'fullname', 'fullname', 'status', null, 'Преподаватели (Фамилия Имя Отчество)'],
        ])->execute();
    }

    /**
     * @return bool|void
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_fio'])->execute();
        $this->db->createCommand()->dropView('teachers_view')->execute();
    }
}
