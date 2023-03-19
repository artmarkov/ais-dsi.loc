<?php


class m220418_114347_add_job_to_qeue_schedule extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSessions', 'title' => 'Удаление сессий ботов', 'content' => 'Удаляет все сессии ботов.', 'cron_expression' => '0 1 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\FlushCache', 'title' => 'Очистка кэш-памяти', 'content' => 'Очищает кэш память', 'cron_expression' => '0 1 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSigur', 'title' => 'Очистка БД проходов через СКУД', 'content' => 'Очищает БД проходов через СКУД (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianAttendLog', 'title' => 'Очистка Журнала выдачи ключей', 'content' => 'Очищает Журнал выдачи ключей (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianSiteLog', 'title' => 'Очистка Лога посещения сайта', 'content' => 'Очищает Лог посещения сайта (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianRequestLog', 'title' => 'Очистка Лога запросов', 'content' => 'Очищает Лог запросов (срок хранения задается в настройках модулей)', 'cron_expression' => '0 1 10 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);

        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianDeletedMailJob', 'title' => 'Уничтожение удаленных писем', 'content' => 'Удаляет все письма физически из корзины.', 'cron_expression' => '0 0 */15 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\MessageNewEmailJob', 'title' => 'Оповещение пользователей о новых сообщениях', 'content' => '', 'cron_expression' => '0 8 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\TrashMailJob', 'title' => 'Очистка всех корзин', 'content' => 'Очищает корзины всех пользователей', 'cron_expression' => '0 0 1 */6 *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);

        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\MailQueue', 'title' => 'Рассылка писем из очереди', 'content' => 'Осуществляет рассылку писем из очереди не более 20 писем в минуту', 'cron_expression' => '* * * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\BirthdayTask', 'title' => 'Дни рождения сегодня', 'content' => 'Осуществляет отправку письма со списком сотрудников и преподавателей, у которых сегодня день рождения', 'cron_expression' => '0 6 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\BirthdayTask']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\MailQueue']);

        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianDeletedMailJob']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\MessageNewEmailJob']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\TrashMailJob']);

        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianRequestLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianSiteLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianAttendLog']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSigur']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\FlushCache']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianLogSessions']);
    }
}
