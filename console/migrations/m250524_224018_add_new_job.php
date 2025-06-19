<?php


class m250524_224018_add_new_job extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\MakeProgressDoc', 'title' => 'Создание архива выписки из Журнала успеваемости', 'content' => 'Создает архив выписки из Журнала успеваемости преподавателей и сохраняет в папку "Документы"', 'cron_expression' => '0 4 1 1,2,3,4,5,6,9,10,11,12 *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\MakeScheduleDoc', 'title' => 'Создание архива выписки из Расписания занятий и Расписания консультаций', 'content' => 'Создает архив выписки из Расписания занятий и Расписания консультаций и сохраняет в папку "Документы"', 'cron_expression' => '10 4 1 1,2,3,4,5,6,9,10,11,12 *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\PlanfixTask', 'title' => 'Уведомления исполнителям модуля "Планировщик заданий"', 'content' => 'Рассылает исполнителям содержание работы и срок выполнения', 'cron_expression' => '0 7 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\PlanfixTask']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\MakeScheduleDoc']);
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\MakeProgressDoc']);
    }
}
