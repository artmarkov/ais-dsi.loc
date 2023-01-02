<?php

use \artsoft\db\BaseMigration;

class m221125_201442_option extends BaseMigration
{
    public function up()
    {

    }

        public function down()
    {
        $this->db->createCommand()->dropView('schoolplan_view')->execute();
    }
}
