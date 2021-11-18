<?php


class m211119_191543_ref extends  \artsoft\db\BaseMigration
{

   public function up()
   {
       $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
           ['users_teachers', 'teachers_view', 'user_id', 'teachers_id', 'user_id', 'status', null, 'Преподаватели (ссылка на id учетной записи)'],
       ])->execute();
   }

}
