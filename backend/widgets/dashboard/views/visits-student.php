<?php

/* @var $this yii\web\View */
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Количество посещений учеников на текущий день (ожидаемое, согласно расписанию)</div>
    <div class="panel-body">
        <?php if ($active): ?>
            <div class="clearfix">
                <?= '<b>Митинская 47 корп.1 (' . $active[1000]['count'] . '),</b> '?>
                <?= '<b>Пятницкое шоссе д.40 (' . $active[1001]['count'] . ')</b> '?>
            </div>
        <?php else: ?>
            <h5><em><?= 'Посещений не планируется.' ?></em></h5>
        <?php endif; ?>
    </div>
</div>