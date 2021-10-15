<?php
/*

CREATE TABLE `queue` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT 0,
  `priority` int(11) unsigned NOT NULL DEFAULT 1024,
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB*/

class m210824_135345_ref extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;

        $this->createTable('queue', [
            'id' => $this->bigPrimaryKey(),
            'channel' => $this->string(255)->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer(11)->notNull(),
            'ttr' => $this->integer(11)->notNull(),
            'delay' => $this->integer(11)->notNull()->defaultValue('0'),
            'priority' => $this->integer(11)->unsigned()->notNull()->defaultValue('1024'),
            'reserved_at' => $this->integer(11)->defaultValue(null),
            'attempt' => $this->integer(11)->defaultValue(null),
            'done_at' => $this->integer(11)->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('channel', 'queue', 'channel');
        $this->createIndex('reserved_at', 'queue', 'reserved_at');
        $this->createIndex('priority', 'queue', 'priority');

    }

    public function down()
    {
        $this->dropTable('queue');
    }
}