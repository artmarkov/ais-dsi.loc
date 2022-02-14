<?php


class m220214_204014_add_table_stadyplan_lesson extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_lesson_mark', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'mark_label' => $this->string(8)->notNull(),
            'mark_hint' => $this->string(64),
            'mark_value' => $this->float(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_lesson_mark', 'Справочник оценок');
        $this->addForeignKey('guide_lesson_mark_ibfk_1', 'guide_lesson_mark', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('guide_lesson_mark_ibfk_2', 'guide_lesson_mark', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->batchInsert('guide_lesson_mark', ['id', 'mark_label', 'mark_hint', 'mark_value', 'status', 'sort_order', 'created_at', 'created_by', 'updated_at', 'updated_by'], [
            [1000, 'ЗЧ', 'Зачет', null, 1, 1000, time(), 1000, time(), 1000],
            [1001, 'НЗ', 'Незачет', null, 1, 1001, time(), 1000, time(), 1000],
            [1002, 'НА', 'Не аттестован', null, 1, 1002, time(), 1000, time(), 1000],
            [1003, '2', null, 2, 1, 1003, time(), 1000, time(), 1000],
            [1004, '3-', null, 2.6, 1, 1004, time(), 1000, time(), 1000],
            [1005, '3', null, 3, 1, 1005, time(), 1000, time(), 1000],
            [1006, '3+', null, 3.5, 1, 1006, time(), 1000, time(), 1000],
            [1007, '4-', null, 3.6, 1, 1007, time(), 1000, time(), 1000],
            [1008, '4', null, 4, 1, 1008, time(), 1000, time(), 1000],
            [1009, '4+', null, 4.5, 1, 1009, time(), 1000, time(), 1000],
            [1010, '5-', null, 4.6, 1, 1010, time(), 1000, time(), 1000],
            [1011, '5', null, 5, 1, 1011, time(), 1000, time(), 1000],
            [1012, '5+', null, 5.5, 1, 1012, time(), 1000, time(), 1000],
            [1013, 'Н', 'Отсутствие по неуважительной причине', null, 1, 1013, time(), 1000, time(), 1000],
            [1014, 'П', 'Отсутствие по уважительной причине', null, 1, 1014, time(), 1000, time(), 1000],
            [1015, 'Б', 'Отсутствие по причине болезни', null, 1, 1015, time(), 1000, time(), 1000],
            [1016, 'О', 'Опоздание на урок', null, 1, 1016, time(), 1000, time(), 1000],
            [1017, '*', 'Присуствие на занятии', null, 1, 1017, time(), 1000, time(), 1000],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_lesson_mark', 1018)->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_mark', 'guide_lesson_mark', 'id', 'mark_label', 'sort_order', 'status', null, 'Список оценок'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['lesson_mark_hint', 'guide_lesson_mark', 'mark_label', 'mark_hint', 'sort_order', 'mark_hint', null, 'Список комментариев к оценкам'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_mark_hint'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'lesson_mark'])->execute();
        $this->dropTable('guide_lesson_mark');

    }
}
