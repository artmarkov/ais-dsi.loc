<?php

use yii\db\Migration;

class m240506_115555_create_table_cost_education extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_cost_education', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'programm_id' => $this->integer()->notNull()->unique()->comment('Программа обучения'),
            'standard_basic' => $this->float()->notNull()->comment('Норматив базовый, руб.'),
            'standard_basic_ratio' => $this->float()->notNull()->comment('Коэффициент к базовому нормативу'),
        ], $tableOptions);

        $this->addCommentOnTable('guide_cost_education', 'Стоимость образовательных услуг');
        $this->db->createCommand()->resetSequence('guide_cost_education', 1000)->execute();
        $this->addForeignKey('guide_cost_education_ibfk_1', 'guide_cost_education', 'programm_id', 'education_programm', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTable('guide_cost_education');
    }
}
