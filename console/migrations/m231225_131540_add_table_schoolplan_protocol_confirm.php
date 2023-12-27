<?php


class m231225_131540_add_table_schoolplan_protocol_confirm extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('schoolplan_protocol_confirm', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->notNull(),
            'confirm_status' => $this->integer()->notNull()->defaultValue(0),
            'teachers_sign' => $this->integer(),
            'sign_message' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_protocol_confirm', 'Утверждение протокола мероприятий');
        $this->addForeignKey('schoolplan_protocol_confirm_ibfk_1', 'schoolplan_protocol_confirm', 'schoolplan_id', 'schoolplan', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_protocol_confirm_ibfk_2', 'schoolplan_protocol_confirm', 'teachers_sign', 'teachers', 'id', 'NO ACTION', 'NO ACTION');


    }

    public function down()
    {
        $this->dropTableWithHistory('schoolplan_protocol_confirm');
    }
}
