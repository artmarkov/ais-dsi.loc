<?php

use common\widgets\qrcode\QRcode;
use common\widgets\qrcode\widgets\Text;
use artsoft\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanInvoices */

$this->params['breadcrumbs'][] = 'Информация о платеже';
$this->params['breadcrumbs'][] = $this->title;

$data = $model->getInvoicesData();

?>
<div class="studyplan-invoices-view">
    <div class="panel">
        <div class="panel-heading">
            Информация о платеже: <b><?= $model->getStatusValue($model->status); ?></b>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading pull-right">

                </div>
                <div class="panel-body">
                    <div class="col-md-4">
                        <?php echo Text::widget([
                            'outputDir' => '@webroot/upload/qrcode',
                            'outputDirWeb' => '@web/upload/qrcode',
                            'ecLevel' => QRcode::QR_ECLEVEL_L,
                            'text' => $model->getQrContent($data),
                            'size' => 2,
                        ]); ?>
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive kv-grid-container">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                <tr>
                                    <td>Получатель:</td>
                                    <td><?= $data['recipient']; ?></td>
                                </tr>
                                <tr>
                                    <td>Банк:</td>
                                    <td><?= $data['bank_name']; ?></td>
                                </tr>
                                <tr>
                                    <td>Расчетный счет:</td>
                                    <td><?= $data['payment_account']; ?></td>
                                </tr>
                                <tr>
                                    <td>Корреспондентский счет:</td>
                                    <td><?= $data['corr_account']; ?></td>
                                </tr>
                                <tr>
                                    <td>Номер лицевого счета получателя:</td>
                                    <td><?= $data['personal_account']; ?></td>
                                </tr>
                                <tr>
                                    <td>Л/сч ученика:</td>
                                    <td><?= $data['student_fls']; ?></td>
                                </tr>
                                <tr>
                                    <td>ИНН:</td>
                                    <td><?= $data['inn']; ?></td>
                                </tr>
                                <tr>
                                    <td>КПП:</td>
                                    <td><?= $data['kpp']; ?></td>
                                </tr>
                                <tr>
                                    <td>БИК:</td>
                                    <td><?= $data['bik']; ?></td>
                                </tr>
                                <tr>
                                    <td>КБК:</td>
                                    <td><?= $data['kbk']; ?></td>
                                </tr>
                                <tr>
                                    <td>ОКТМО:</td>
                                    <td><?= $data['oktmo']; ?></td>
                                </tr>
                                <tr>
                                    <td>Назначение платежа:</td>
                                    <td><?= $data['invoices_app']; ?></td>
                                </tr>
                                <tr>
                                    <td>Сумма платежа:</td>
                                    <td><b><?= $data['invoices_summ']; ?> руб.</b></td>
                                </tr>
                                <tr>
                                    <td>Период оплаты:</td>
                                    <td><b><?= $data['pay_period']; ?></b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::exitButton(); ?>
                <?= Html::a('<span class="fa fa-file-word-o" aria-hidden="true""></span> Скачать квитанцию',
                    [\artsoft\Art::isFrontend() ? (\artsoft\models\User::hasRole(['student']) ? '/studyplan/default/make-invoices' : '/teachers/studyplan/make-invoices') : '/invoices/default/make-invoices', 'id' => $model->id], [
                        'title' => 'Скачать квитанцию',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-md'
                    ]
                );
                ?>
            </div>
        </div>
    </div>
</div>