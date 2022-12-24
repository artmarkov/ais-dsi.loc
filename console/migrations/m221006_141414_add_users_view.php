<?php

use \artsoft\db\BaseMigration;

class m221006_141414_add_users_view extends BaseMigration
{
    public function up()
    {

        $this->db->createCommand()->createView('users_view', '
       SELECT users.id,
    users.username,
    users.email,
    users.email_confirmed,
    users.superadmin,
    (SELECT array_to_string((( SELECT array_agg(auth_item.description) AS array_agg
                   FROM auth_item
                     JOIN auth_assignment ON auth_assignment.item_name::text = auth_item.name::text
                  WHERE auth_assignment.user_id = users.id))::character varying[], \',\'::text) AS array_to_string) AS roles,
    users.registration_ip,
    users.status,
    user_common.id AS user_common_id,
        CASE
            WHEN user_common.user_category::text = \'employees\'::text THEN \'Сотрудник\'::text
            WHEN user_common.user_category::text = \'teachers\'::text THEN \'Преподаватель\'::text
            WHEN user_common.user_category::text = \'students\'::text THEN \'Ученик\'::text
            WHEN user_common.user_category::text = \'parents\'::text THEN \'Родитель\'::text
            WHEN user_common.user_category IS NULL THEN \'Система\'::text
            ELSE NULL::text
        END AS user_category_name,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS user_name,
    user_common.phone,
    user_common.phone_optional,
    user_common.status AS user_common_status
   FROM users
     LEFT JOIN user_common ON user_common.user_id = users.id
  ORDER BY users.id;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('users_view')->execute();
    }
}
