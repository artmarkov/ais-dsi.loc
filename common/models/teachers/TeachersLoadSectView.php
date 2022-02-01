<?php

namespace common\models\teachers;

use artsoft\helpers\RefBook;
use artsoft\widgets\Notice;
use artsoft\widgets\Tooltip;

class TeachersLoadSectView extends TeachersLoadView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load_sect_view';
    }

    public function getTeachersFullLoad()
    {
        return self::find()
            ->select(new \yii\db\Expression('SUM(load_time)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }

    public function getStudyplanWeekTime()
    {
        $funcSql = <<< SQL
    select MAX(week_time)
	from studyplan_subject 
	where id = any(string_to_array('{$this->studyplan_subject_list}', ',')::int[])
SQL;

        return $this->studyplan_subject_list ? \Yii::$app->db->createCommand($funcSql)->queryScalar() : 0;
    }

    /**
     * Проверка на необходимость добавления нагрузки
     * @return bool
     */
    public function getTeachersLoadsNeed()
    {
        return $this->getTeachersFullLoad() < $this->getStudyplanWeekTime();
    }

    public function getItemLoadNotice()
    {
        $tooltip = [];
        if ($this->studyplan_subject_list == '') {
            $message = 'В группе ' . RefBook::find('sect_name_2')->getValue($this->subject_sect_studyplan_id) . ' не обнаружено ни одного учащегося!';
            Notice::registerWarning($message);
        }
        if ($this->teachers_load_id) {
            if ($this->getTeachersLoadsNeed()) {
                $message = 'Суммарное время нагрузки не соответствует планированию - ' . $this->getStudyplanWeekTime() . ' ак.ч';
                $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
            }
            return implode('', $tooltip);
        }
        return null;
    }
}
