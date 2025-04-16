<?php

namespace common\models\teachers;

use artsoft\widgets\Tooltip;
use common\models\guidejob\Direction;
use common\models\studyplan\Studyplan;
use common\models\user\UserCommon;

trait TeachersLoadTrait
{

    /**
     * Получение всех нагрузок дисциплин по текущему преподавателю
     * @param $teachers_id
     * @return array
     */
    public static function getTeachersSubjectAll($teachers_id)
    {
        $query1 = TeachersLoadView::find()
            ->select('subject_sect_studyplan_id')
            ->distinct()
            ->where(['studyplan_subject_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();
        $query2 = TeachersLoadView::find()
            ->select('studyplan_subject_id')
            ->distinct()
            ->where(['subject_sect_studyplan_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();

        return TeachersLoadView::find()
            ->select('teachers_load_id')
            ->distinct()
            ->where(['IS NOT', 'teachers_load_id', null])
            ->andWhere(['subject_sect_studyplan_id' => $query1])
            ->orWhere(['studyplan_subject_id' => $query2])
            ->column();
    }

    public static function getTeachersForTeachersLoad($teachers_id)
    {
       $teachersLoadIds = self::getTeachersSubjectAll($teachers_id);

        $teachersIds = TeachersLoadView::find()
            ->select('teachers_id')
            ->distinct()
            ->where(['teachers_load_id' => $teachersLoadIds])
            ->column();

        $query = Teachers::find()
            ->select(['teachers.id as id', 'CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as name'])
            ->joinWith(['user'])
            ->where(['teachers.id' => $teachersIds])
            ->andWhere(['=', 'status', UserCommon::STATUS_ACTIVE])
            ->asArray()
            ->all();
        return \yii\helpers\ArrayHelper::map($query, 'id', 'name');
    }

    public function getTeachersStudyplanFullLoad()
    {
        return TeachersLoadStudyplanView::find()
            ->select(new \yii\db\Expression('SUM(load_time)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }

    public function getTeachersFullLoad()
    {
        return TeachersLoadView::find()
            ->select(new \yii\db\Expression('SUM(load_time)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }

    public function getTeachersStudyplanFullLoadConsult()
    {
        return TeachersLoadStudyplanView::find()
            ->select(new \yii\db\Expression('SUM(load_time_consult)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }

    public function getTeachersFullLoadConsult()
    {
        return TeachersLoadView::find()
            ->select(new \yii\db\Expression('SUM(load_time_consult)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }

    /**
     *  Список группы
     * @return array
     */
    public function getSectList()
    {
        $studentsFio = [];

        if ($this->subject_sect_studyplan_id !== null) {
            $studentsFio = (new \yii\db\Query())->select('student_fullname')->from('studyplan_subject_view')->distinct()
                ->where(new \yii\db\Expression("studyplan_subject_id = any (string_to_array('{$this->studyplan_subject_list}', ',')::int[])"))
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 3, 4]]
                    ]
                ])->orderBy('student_fullname')
                ->column();
        }
        return $studentsFio;
    }

    public function getSectNotice()
    {
        $tooltip = [];
        $studentsFio = $this->getSectList();
        if ($this->subject_sect_studyplan_id !== 0) {
            if ($this->studyplan_subject_list == '') {
//                $message = 'Группа ' . RefBook::find('sect_name_2')->getValue($this->subject_sect_studyplan_id) . ' не заполнена';
                $message = 'Группа не заполнена';
                //Notice::registerWarning($message);
                $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
            } else {
                $message = 'Группа (' . count($studentsFio) . '): ' . implode(', ', $studentsFio);
                $tooltip[] = Tooltip::widget(['type' => 'info', 'message' => $message]);
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

    public function getItemLoadStudyplanNotice()
    {
        $tooltip = [];

        if ($this->teachers_load_id) {
            if (!Direction::isDirectionSlave($this->direction_id)) {
                if ($this->getTeachersStudyplanFullLoad() != $this->week_time) {
                    $message = 'Суммарное время нагрузки ' . $this->getTeachersStudyplanFullLoad() . ' ак.ч не соответствует планированию - ' . $this->week_time . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

    public function getItemLoadNotice()
    {
        $tooltip = [];

        if ($this->teachers_load_id) {
            if (!Direction::isDirectionSlave($this->direction_id)) {
                if ($this->getTeachersFullLoad() != $this->week_time) {
                    $message = 'Суммарное время нагрузки ' . $this->getTeachersFullLoad() . ' ак.ч не соответствует планированию - ' . $this->week_time . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

    public function getItemLoadStudyplanConsultNotice()
    {
        $tooltip = [];
        if ($this->teachers_load_id) {
            if (!Direction::isDirectionSlave($this->direction_id)) {
                if ($this->getTeachersStudyplanFullLoadConsult() != $this->year_time_consult) {
                    $message = 'Суммарное время консультаций ' . $this->getTeachersStudyplanFullLoadConsult() . ' ак.ч не соответствует планированию - ' . $this->year_time_consult . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

    public function getItemLoadConsultNotice()
    {
        $tooltip = [];
        if ($this->teachers_load_id) {
            if (!Direction::isDirectionSlave($this->direction_id)) {
                $yearTime = $this->getTeachersFullLoadConsult();
                if ($yearTime != $this->year_time_consult) {
                    $message = 'Суммарное время консультаций ' . $yearTime . ' ак.ч. не соответствует планированию - ' . $this->year_time_consult . ' ак.ч';
                    $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
                }
            }
            return implode(' ', $tooltip);
        }
        return null;
    }

}
