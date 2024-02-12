<?php

namespace console\jobs;

use common\models\studyplan\StudyplanInvoices;
use Yii;

/**
 * Задолженность по оплате,
 * Class PaymentDebtorsTask.
 */
class PaymentDebtorsTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        ;
        $timestamp = time() - Yii::$app->settings->get('module.debtors_days', 60) * 24 * 3600;
        $where = ['AND', ['<', 'invoices_reporting_month', $timestamp], ['=', 'status', StudyplanInvoices::STATUS_WORK], ['=','invoices_id', 1000]];
        StudyplanInvoices::updateAll(['status' => StudyplanInvoices::STATUS_ARREARS], $where);
    }
}
