<?php

namespace common\models\history;

use common\models\own\Invoices;
use common\widgets\history\BaseHistory;

class InvoicesHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'invoices_hist';
    }

    public static function getModelName()
    {
        return Invoices::class;
    }

    protected function getFields()
    {
        return [
            'name',
            'recipient',
            'inn',
            'kpp',
            'payment_account',
            'corr_account',
            'personal_account',
            'bank_name',
            'bik',
            'oktmo',
            'kbk',
        ];
    }

}