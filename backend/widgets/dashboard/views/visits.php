<?php

/* @var $this yii\web\View */
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Количество посещений учеников/преподавателей на <span
                style="color: #921e12"><?= $date ?></span> (согласно расписанию)
    </div>
    <div class="panel-body">
        <?php if ($active): ?>
            <div class="clearfix">
                <?php

                $text = '';
                foreach (\common\models\auditory\AuditoryBuilding::getAuditoryBuildingListByAddress() as $id => $name) {
                    $count_student = 0;
                    $count_teachers = 0;
                    if (isset($active[$id])) {
                        $count_student = $active[$id]['count_student'] ?? 0;
                        $count_teachers = $active[$id]['count_teachers'] ?? 0;
                    }
                    $text .= '<b>' . $name . ': <span style="color: #921e12; font-size: larger">' . $count_student . '/' . $count_teachers . '</span><BR/></b> ';
                }
                echo $text;
                ?>
            </div>
        <?php else: ?>
            <h5><em><?= 'Посещений не планируется.' ?></em></h5>
        <?php endif; ?>
    </div>
</div>