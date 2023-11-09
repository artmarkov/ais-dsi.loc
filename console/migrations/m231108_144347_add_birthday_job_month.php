<?php


class m231108_144347_add_birthday_job_month extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\BirthdayPeriodTask', 'title' => 'Дни рождения на следующий месяц', 'content' => 'Осуществляет отправку письма со списком сотрудников и преподавателей, у которых день рождения на следующий месяц', 'cron_expression' => '0 6 25 * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\BirthdayPeriodTask']);
    }
}
