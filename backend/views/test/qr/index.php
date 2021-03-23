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
    'text' => 'ST00012|Name=ГБУДО г. Москвы "ДШИ им.И.Ф.Стравинского")|PersonalAcc=03224643450000007300|BankName=ГУ Банка России по ЦФО//УФК по г.Москве г.Москва|BIC=004525988|CorrespAcc=40102810545370000003|Sum=340000|Purpose= Оплата за март 2021 г.ВН|PayeeINN=7733098705|KPP=773301001|CBC=05600000000131131022|OKATO=|lastName=Туйцына|firstName=Анастасия|middleName=Евгеньевна|persAcc=03992|childFio=Туйцына Анастасия Евгеньевна|paymPeriod=Март, 2021 г.|instNum=ДШИ им.И.Ф.Стравинского|classNum=Веселые нотки',
    'size' => 2,
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