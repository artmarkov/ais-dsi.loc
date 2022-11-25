<?php

use \artsoft\db\BaseMigration;

class m221125_201442_option extends BaseMigration
{
    public function up()
    {

        $this->db->createCommand()->createView('users_view', '
       select users.id as id,
              users.username,
              users.email,
              users.email_confirmed,
              users.superadmin,
              users.registration_ip,
              users.status,
              user_common.id as user_common_id,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудник\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватель\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученик\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родитель\'
              WHEN (user_common.user_category IS null) THEN \'Система\'
            END AS user_category_name,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS user_name, 
            user_common.phone,
            user_common.phone_optional,
            user_common.status as user_common_status
        from users 
        left join user_common on (user_common.user_id = users.id)
        order by id;
        ')->execute();
    }

    public function down()
    {

    }
}
