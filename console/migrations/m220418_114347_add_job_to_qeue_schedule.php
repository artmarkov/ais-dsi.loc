<?php


class m220418_114347_add_job_to_qeue_schedule extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSessions', 'title' => 'Удаление сессий ботов', 'content' => 'Удаляет все сессии ботов.', 'cron_expression' => '0 1 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\FlushCache', 'title' => 'Очистка кэш-памяти', 'content' => 'Очищает кэш память', 'cron_expression' => '0 1 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSigur', 'title' => 'Очистка БД проходов через СКУД', 'content' => 'Очищает БД проходов через СКУД (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianAttendLog', 'title' => 'Очистка Журнала выдачи ключей', 'content' => 'Очищает Журнал выдачи ключей (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianSiteLog', 'title' => 'Очистка Лога посещения сайта', 'content' => 'Очищает Лог посещения сайта (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianRequestLog', 'title' => 'Очистка Лога запросов', 'content' => 'Очищает Лог запросов (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 1000, 'updated_by' => 1000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianRequestLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianSiteLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianAttendLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSigur']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\FlushCache']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSessions']);
    }
}
