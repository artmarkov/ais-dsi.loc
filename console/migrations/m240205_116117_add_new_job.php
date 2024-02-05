<?php


class m240205_116117_add_new_job extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->insert('{{%queue_schedule}}', ['class' => 'console\jobs\PaymentDebtorsTask', 'title' => 'Задолженность по оплате', 'content' => 'Переводит счета в статус "Задолженность по оплате" и отправляет уведомления на внутреннюю почту', 'cron_expression' => '0 2 * * *', 'priority' => 1024 ,'created_at' => time(), 'updated_at' => time(), 'created_by' => 10000, 'updated_by' => 10000]);
    }

    public function down()
    {
        $this->delete('{{%queue_schedule}}', ['class' => 'console\jobs\PaymentDebtorsTask']);
    }
}
