<?php

use yii\db\Migration;

class m210309_064940_03_create_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('activities_ibfk_2', '{{%activities}}', 'auditory_id', '{{%auditory}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        echo "m210309_064940_03_create_foreign_keys cannot be reverted.\n";
        return false;
    }
}
