<?php


class m250305_235918_add_new_job extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\MakeDump', 'title' => 'Создание дампа БД', 'content' => 'Создает дамп текущей Базы Данных', 'cron_expression' => '0 3 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\MakeDump']);
    }
}
