<?php

namespace common\models\teachers;

use common\models\schoolplan\SchoolplanPerform;
use Yii;

/**
 * This is the model class for table "portfolio_view".
 *
 */
class PortfolioView extends SchoolplanPerform
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'portfolio_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['schoolplan_perform_id'] = Yii::t('art/student', 'schoolplan_perform_id');
        $attr['schoolplan_id'] = Yii::t('art/guide', 'schoolplan_id');
        $attr['title'] = 'Название мероприятия';
        $attr['datetime_in'] = 'Дата и время начала';
        $attr['datetime_out'] = 'Дата и время окончания';
        $attr['category_id'] = Yii::t('art/guide', 'category_id');
        $attr['doc_status'] = 'Статус мероприятия';
        $attr['mark_label'] = Yii::t('art/guide', 'Mark Label');
        $attr['sect_name'] = Yii::t('art/guide', 'Sect Name');
        $attr['subject'] = Yii::t('art/guide', 'Subject');
        $attr['subject_vid_id'] = Yii::t('art/guide', 'Subject Vid');
        $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
        $attr['studyplan_status'] = Yii::t('art/guide', 'Studyplan Status');

        return $attr;
    }
}
