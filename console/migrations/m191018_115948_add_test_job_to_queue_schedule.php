<?php

use yii\db\Migration;

class m191018_115948_add_test_job_to_queue_schedule extends Migration
{
    public function up()
    {
       $this->insert('{{%queue_schedule}}', ['class' => 'artsoft\queue\jobs\TestJob', 'title' => 'Тестовое задание',  'content' => 'Используется для тестирования приложения.', 'cron_expression' => '*/2 * * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1, 'updated_by' => 1]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'artsoft\queue\jobs\TestJob']);
    }
}
