<?php

namespace common\models\teachers;

use artsoft\helpers\RefBook;
use artsoft\widgets\Notice;
use artsoft\widgets\Tooltip;
use common\models\guidejob\Direction;

trait TeachersLoadTrait
{

    /**
     * Получение всех нагрузок дисциплин по текущему преподавателю
     * @param $teachers_id
     * @return array
     */
    public static function getTeachersSubjectAll($teachers_id)
    {
        $query1 = self::find()
            ->select('subject_sect_studyplan_id')
            ->distinct()
            ->where(['studyplan_subject_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();
        $query2 = self::find()
            ->select('studyplan_subject_id')
            ->distinct()
            ->where(['subject_sect_studyplan_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();

        return self::find()
            ->where(['subject_sect_studyplan_id' => $query1])
            ->orWhere(['studyplan_subject_id' => $query2])
            ->column();
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

    public function getItemLoadNotice()
    {
        $tooltip = [];
        if ($this->studyplan_subject_list == '') {
            $message = 'В группе ' . RefBook::find('sect_name_2')->getValue($this->subject_sect_studyplan_id) . ' не обнаружено ни одного учащегося!';
            Notice::registerWarning($message);
        }
        if ($this->teachers_load_id) {
            if (!Direction::isDirectionSlave($this->direction_id)) {
                if ($this->getTeachersFullLoad() != $this->week_time) {
                    $message = 'Суммарное время нагрузки ' . $this->getTeachersFullLoad() . ' ак.ч не соответствует планированию - ' . $this->week_time . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            } else {
                if ($this->getTeachersFullLoad() > $this->week_time) {
                    $message = 'Суммарное время нагрузки ' . $this->getTeachersFullLoad() . ' ак.ч не соответствует планированию - ' . $this->week_time . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            }
            return implode('', $tooltip);
        }
        return null;
    }
}
