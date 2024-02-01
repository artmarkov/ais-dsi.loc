<?php

namespace console\jobs;

use Yii;

/**
 * Удаляет дубли из таблиц lesson_progress,
 * Class ClianDoubleData.
 */
class ClianDoubleData extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $funcSql = <<< SQL
                DELETE FROM lesson_progress a USING (
                        SELECT MIN(ctid) as ctid, lesson_items_id, studyplan_subject_id, lesson_mark_id
                        FROM lesson_progress 
                            GROUP BY (lesson_items_id, studyplan_subject_id, lesson_mark_id) HAVING COUNT(*) > 1
                    ) b
                WHERE a.lesson_items_id = b.lesson_items_id
                AND a.studyplan_subject_id = b.studyplan_subject_id
                AND a.lesson_mark_id = b.lesson_mark_id
                AND a.ctid <> b.ctid;
SQL;
        Yii::$app->db->createCommand($funcSql)->query();
    }

}
