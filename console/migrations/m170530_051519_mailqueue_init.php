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
    const QUEUE_TABLE = 'mail_queue';

    public function up()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		
		$this->createTable(self::QUEUE_TABLE, [
			'id' => Schema::TYPE_PK,
			'subject' => Schema::TYPE_STRING,
			'swift_message' => Schema::TYPE_TEXT,
			'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
			'time_to_send' => Schema::TYPE_DATETIME . ' NOT NULL',
			'sent_time' => Schema::TYPE_DATETIME . ' DEFAULT NULL',
			'attempts' => Schema::TYPE_INTEGER,
			'last_attempt_time' => Schema::TYPE_DATETIME . ' DEFAULT NULL',
		], $tableOptions);
		
        $this->createIndex('IX_time_to_send', self::QUEUE_TABLE, 'time_to_send');
        $this->createIndex('IX_sent_time', self::QUEUE_TABLE, 'sent_time');
        $this->addCommentOnTable(self::QUEUE_TABLE, 'Журнал отправленных email-сообщений');
    }

    public function down()
    {
        $this->dropTable(self::QUEUE_TABLE);
    }
}
