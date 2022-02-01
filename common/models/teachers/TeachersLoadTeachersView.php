<?php

namespace common\models\teachers;

class TeachersLoadTeachersView extends TeachersLoadView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load_teachers_view';
    }
}
