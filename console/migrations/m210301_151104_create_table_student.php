<?php

use yii\db\Migration;

class m210301_151104_create_table_student extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student_position}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
            'status' => $this->smallInteger(1)->unsigned()->notNull(),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%student_position}}', ['id', 'name', 'slug', 'status'], [
            [1, 'Абитуриент', 'Абит', 1],
            [2, 'Учащийся', 'Уч-ся', 1],
            [3, 'Выпускной класс', 'Вып.кл', 1],
            [4, 'Окончил обучение', 'Окон', 1],
            [5, 'Отчислен', 'Отч', 1],
        ])->execute();

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
        $this->addForeignKey('student_ibfk_2', '{{%student}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTable('{{%student}}');
        $this->dropTable('{{%student_position}}');
    }
}
