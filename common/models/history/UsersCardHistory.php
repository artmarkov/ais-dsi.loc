<?php

namespace common\models\history;

use common\models\service\UsersCard;
use common\widgets\history\BaseHistory;

class UsersCardHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'users_card_hist';
    }

    public static function getModelName()
    {
        return UsersCard::class;
    }

    protected function getFields()
    {
        return [
            'timestamp_deny',
            'photo_ver',
            'key_hex',
            'mode_main',
            'mode_list',
        ];
    }
}