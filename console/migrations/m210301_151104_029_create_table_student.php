<?php

use yii\db\Migration;

class m210301_151104_029_create_table_student extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student}}', [
            'id' => $this->integer(8)->unsigned()->notNull(),
            'user_id' => $this->integer(),
            'position_id' => $this->tinyInteger(2)->unsigned(),
            'sertificate_name' => $this->string(32),
            'sertificate_series' => $this->string(32),
            'sertificate_num' => $this->string(32),
            'sertificate_organ' => $this->string(127),
            'sertificate_timestamp' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('id', '{{%student}}', 'id', true);
        $this->createIndex('position_id', '{{%student}}', 'position_id');
        $this->createIndex('user_id', '{{%student}}', 'user_id');
        $this->addForeignKey('student_ibfk_1', '{{%student}}', 'position_id', '{{%student_position}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%student}}');
    }
}
