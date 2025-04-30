<?php

/* @var $this yii\web\View */
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Количество посещений учеников/преподавателей на <span style="color: #921e12"><?= $date ?></span> (согласно расписанию)</div>
    <div class="panel-body">
        <?php if ($active): ?>
            <div class="clearfix">
                <?= '<b>Митинская 47 корп.1 : <span style="color: #921e12; font-size: larger">' . $active[1000]['count_student'] . '/' . $active[1000]['count_teachers'] . '</span><BR/></b> '?>
                <?= '<b>Пятницкое шоссе д.40 : <span style="color: #921e12; font-size: larger">' . $active[1001]['count_student'] . '/' . $active[1001]['count_teachers'] . '</span></b> '?>
            </div>
        <?php else: ?>
            <h5><em><?= 'Посещений не планируется.' ?></em></h5>
        <?php endif; ?>
    </div>
</div>