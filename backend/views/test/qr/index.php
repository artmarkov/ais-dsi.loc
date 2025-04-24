<?php

use common\widgets\qrcode\QRcode;
use common\widgets\qrcode\widgets\Email;
use common\widgets\qrcode\widgets\Text;

?>
    <hr>
<?php
echo Text::widget([
    'outputDir' => '@webroot/upload/qrcode',
    'outputDirWeb' => '@web/upload/qrcode',
    'ecLevel' => QRcode::QR_ECLEVEL_L,

    'text' => 'ST00012|Name=Департамент финансов города Москвы (ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского", л/с 2605642000830080)|PersonalAcc=03224643450000007300|BankName=ГУ Банка России по ЦФО//УФК по г.Москве г.Москва|PersonalAcc=0322464345|BIC=004525988|CorrespAcc=40102810545370000003|Sum=1000000|Purpose=Пленэрная практика|PayeeINN=7733098705|KPP=773301001|CBC=05600000000131131022|ОКТМО=45367000',
    'size' => 3,
]);
?>
    <hr>
<?php
echo Text::widget([
    'outputDir' => '@webroot/upload/qrcode',
    'outputDirWeb' => '@web/upload/qrcode',
    'ecLevel' => QRcode::QR_ECLEVEL_L,
    'text' => 'ST00012|Name=Департамент финансов города Москвы (ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского", л/с 2605642000830080)|PersonalAcc=03224643450000007300|BankName=ГУ Банка России по ЦФО//УФК по г.Москве г.Москва|PersonalAcc=0322464345|BIC=004525988|CorrespAcc=40102810545370000003|Sum=300000|Purpose=Консультации для поступающих на художественное отделение|PayeeINN=7733098705|KPP=773301001|CBC=05600000000131131022|ОКТМО=45367000',
    'size' => 3,
]);
?>
    <hr>
<?php
echo Text::widget([
    'outputDir' => '@webroot/upload/qrcode',
    'outputDirWeb' => '@web/upload/qrcode',
    'ecLevel' => QRcode::QR_ECLEVEL_L,
    'text' => 'ST00012|Name=Департамент финансов города Москвы (ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского", л/с 2605642000830080)|PersonalAcc=03224643450000007300|BankName=ГУ Банка России по ЦФО//УФК по г.Москве г.Москва|PersonalAcc=0322464345|BIC=004525988|CorrespAcc=40102810545370000003|Sum=300000|Purpose=Консультации для поступающих на художественное отделение|PayeeINN=7733098705|KPP=773301001|CBC=05600000000131131022|ОКТМО=45367000',
    'size' => 3,
]);
?>
    <hr>
<?php
//Widget create a Action URL //QR Create by Action
echo Text::widget([
    'outputDir' => '@webroot/upload/qrcode',
    'outputDirWeb' => '@web/upload/qrcode',
    'ecLevel' => QRcode::QR_ECLEVEL_L,
    'text' => 'ST00012|Name=АО "Мосэнергосбыт"|PersonalAcc=40702810338360027201|BankName=ПАО "Сбербанк России"|BIC=044525225|CorrespAcc=30101810400000000225|Purpose=Оплата электроэнергии|Sum=61069|persAcc=2287008779|paymPeriod=022021|regType=6|TechCode=02|PayeeINN=7736520080|KPP=997650001',
    'size' => 3,
]);

////other type
////Create EMAIL
//echo Email::widget([
//    'email' => 'aaaa@gmail.com',
//    'subject' => 'myMail',
//    'body' => 'do something',
//]);
//
////Create Card
//echo \common\widgets\qrcode\widgets\Card::widget([
//    'actions' => ['clientQrcode'],
//    'name' => 'SB',
//    'phone' => '1111111111111',
//    //here jpeg file is only 40x40, grayscale, 50% quality!
//    'avatar' => '@webroot/avatar.jpg',
//]);
//
////Create Sms
//echo \common\widgets\qrcode\widgets\Smsphone::widget([
//    'actions' => ['clientQrcode'],
//    'phone' => '131111111111',
//]);
//
////Create Tel
//echo \common\widgets\qrcode\widgets\Telphone::widget([
//    'actions' => ['clientQrcode'],
//    'phone' => '131111111111',
//]);