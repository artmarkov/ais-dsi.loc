<?php


class m231012_222247_add_job_to_qeue_preregistration extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\ClianPreregistrationList', 'title' => 'Очистка Списка предварительной записи от отчисленных учеников', 'content' => 'Меняет статус предварительной записи на План закрыт.', 'cron_expression' => '0 1 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\ClianPreregistrationList']);
    }
}
