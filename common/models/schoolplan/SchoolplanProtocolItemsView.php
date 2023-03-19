<?php

namespace common\models\schoolplan;

/**
 * * This is the model class for table "schoolplan_protocol_items_view".
 *
 * @property int $id
 * @property int $studyplan_id
 * @property int|null $schoolplan_protocol_id Протокол
 * @property int $studyplan_subject_id Учебный предмет ученика
 * @property string|null $thematic_items_list Список заданий из тематич/реп плана
 * @property int $lesson_mark_id Оцкнка
 * @property string|null $winner_id Звание/Диплом
 * @property string $resume Отзыв комиссии/Результат
 * @property int|null $status_exe Статус выполнения
 * @property int|null $status_sign Статус утверждения
 * @property int|null $signer_id Подписант
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property string $protocol_name Название протокола
 * @property int $protocol_date Дата протокола
 *
 * @property string|null $title Название мероприятия
 * @property int $datetime_in Дата и время начала
 * @property int $datetime_out Дата и время окончания
 *
 */
class SchoolplanProtocolItemsView extends SchoolplanProtocolItems
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_protocol_items_view';
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['studyplan_id'] = 'Studyplan';
        $attr['schoolplan_id'] = 'Schoolplan';
        $attr['protocol_name'] = 'Название протокола';
        $attr['protocol_date'] = 'Дата протокола';
        $attr['title'] = 'Название мероприятия';
        $attr['datetime_in'] = 'Дата и время начала';
        $attr['datetime_out'] = 'Дата и время окончания';

        return $attr;
    }

}
