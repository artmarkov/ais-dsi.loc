<?php


class m260427_171118_add_new_job extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\SchoolplanActivityTask', 'title' => 'Уведомления исполнителям модуля "Планировщик мероприятия"', 'content' => 'Рассылает исполнителям содержание работы и срок выполнения', 'cron_expression' => '0 7 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\SchoolplanActivityTask']);
    }
}
