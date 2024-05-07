<?php


class m240205_116117_add_new_job extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\PaymentDebtorsTask', 'title' => 'Задолженность по оплате', 'content' => 'Переводит счета в статус "Задолженность по оплате" и отправляет уведомления на внутреннюю почту', 'cron_expression' => '0 2 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\WorkingTimeLogTask', 'title' => 'Лог посещаемости', 'content' => 'Записывает в лог посещаемости время начала и окончания работы по расписанию и фактическое время прохода(полученя ключей) за рабочий день', 'cron_expression' => '0 23 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\WorkingTimeSendTask', 'title' => 'Отправка сообщений о посещаемости', 'content' => 'Отправляет письма с информацией о посещаемости на основании информации из лога посещаемости(формируется задачей WorkingTimeLogTask) за рабочий день', 'cron_expression' => '10 23 * * 1-6', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianMailQueueTask', 'title' => 'Очистка очереди неотправленных писем', 'content' => 'Очищает очередь неотправленных писем', 'cron_expression' => '0 3 */3 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianMailQueueTask']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\WorkingTimeSendTask']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\WorkingTimeLogTask']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\PaymentDebtorsTask']);
    }
}
