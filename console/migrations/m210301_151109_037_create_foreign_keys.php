<?php

use yii\db\Migration;

class m210301_151109_037_create_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('creative_works_ibfk_2', '{{%creative_works}}', 'updated_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_ibfk_3', '{{%creative_works}}', 'created_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_author_ibfk_1', '{{%creative_works_author}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_author_ibfk_2', '{{%creative_works_author}}', 'author_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_revision_ibfk_1', '{{%creative_works_revision}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_revision_ibfk_2', '{{%creative_works_revision}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('student_ibfk_2', '{{%student}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('venue_place_ibfk_4', '{{%venue_place}}', 'created_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('venue_place_ibfk_5', '{{%venue_place}}', 'updated_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_department_ibfk_1', '{{%creative_works_department}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        echo "m210301_151109_037_create_foreign_keys cannot be reverted.\n";
        return false;
    }
}
