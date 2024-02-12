<?php

namespace backend\controllers\test;

use common\models\studyplan\StudyplanInvoices;
use Yii;

/**
 * RestoryController between 13520 and 13556
 */
class RestoryController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $funcSql = <<< SQL
            SELECT studyplan_id, invoices_id, direction_id, teachers_id, type_id, vid_id, month_time_fact, invoices_tabel_flag, invoices_date, invoices_summ, payment_time, payment_time_fact, invoices_app, invoices_rem, status, created_at, created_by, updated_at, updated_by, version, invoices_reporting_month, mat_capital_flag
	        FROM studyplan_invoices_hist
            WHERE hist_id  between 13520 and 13556;
		
SQL;
        $models = Yii::$app->db->createCommand($funcSql)->queryAll();
        foreach ($models as $item => $model) {
        $m = new StudyplanInvoices();
            $m->studyplan_id = $model['studyplan_id'];
            $m->invoices_id = $model['invoices_id'];
            $m->direction_id = $model['direction_id'];
            $m->teachers_id = $model['teachers_id'];
            $m->type_id = $model['type_id'];
            $m->vid_id = $model['vid_id'];
            $m->month_time_fact = $model['month_time_fact'];
            $m->invoices_tabel_flag = $model['invoices_tabel_flag'];
            $m->invoices_date = $model['invoices_date'];
            $m->invoices_summ = $model['invoices_summ'];
            $m->payment_time = $model['payment_time'];
            $m->payment_time_fact = $model['payment_time_fact'];
            $m->invoices_app = $model['invoices_app'];
            $m->invoices_rem = $model['invoices_rem'];
            $m->status = $model['status'];
            $m->version = $model['version'];
            $m->invoices_reporting_month = date('m.Y', $model['invoices_reporting_month']);
            $m->mat_capital_flag = $model['mat_capital_flag'];
            $m->save(false);
        }
        echo '<pre>' . print_r(count($models), true) . '</pre>';
    }


}