<?php

use yii\db\Schema;
use yii\db\Migration;
use nterms\mailqueue\MailQueue;

/**
 * Initializes the db table for MailQueue
 *
 * @author Saranga Abeykoon <amisaranga@gmail.com>
 */
class m170530_051519_mailqueue_init extends Migration
{
    public function up()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		
		$this->createTable('mail_queue', [
			'id' => Schema::TYPE_PK,
			'subject' => Schema::TYPE_STRING,
			'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
			'attempts' => Schema::TYPE_INTEGER,
			'last_attempt_time' => Schema::TYPE_DATETIME . ' DEFAULT NULL',
			'sent_time' => Schema::TYPE_DATETIME . ' DEFAULT NULL',
		], $tableOptions);
		
        $this->addColumn('mail_queue', 'time_to_send', $this->dateTime()->notNull());
        $this->createIndex('IX_time_to_send', 'mail_queue', 'time_to_send');
        $this->addColumn('mail_queue', 'swift_message', 'text');
        $this->createIndex('IX_sent_time', 'mail_queue', 'sent_time');
        $this->addCommentOnTable('mail_queue', 'Журнал отправленных email-сообщений');
    }

    public function down()
    {
        $this->dropTable('mail_queue');
    }
}
