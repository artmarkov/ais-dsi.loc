<?php

use yii\db\Migration;

class m210301_151104_create_table_student extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_student_position', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(128),
            'slug' => $this->string(32),
            'status' => $this->smallInteger(1)->unsigned()->notNull(),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('guide_student_position', ['id', 'name', 'slug', 'status'], [
            [1, 'Абитуриент', 'Абит', 1],
            [2, 'Учащийся', 'Уч-ся', 1],
            [3, 'Выпускной класс', 'Вып.кл', 1],
            [4, 'Окончил обучение', 'Окон', 1],
            [5, 'Отчислен', 'Отч', 1],
        ])->execute();

        $this->createTableWithHistory('students', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_common_id' => $this->integer(),
            'position_id' => $this->integer(),
            'sert_name' => $this->string(32),
            'sert_series' => $this->string(32),
            'sert_num' => $this->string(32),
            'sert_organ' => $this->string(127),
            'sert_date' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('students', 1000)->execute();
        $this->addForeignKey('student_ibfk_1', 'students', 'position_id', 'guide_student_position', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropForeignKey('student_ibfk_1', 'student');
        $this->dropTable('student');
        $this->dropTable('guide_student_position');
    }
}
