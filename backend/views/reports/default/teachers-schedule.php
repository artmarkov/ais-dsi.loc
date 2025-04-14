<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;

$stat = [];
$total[1000][1000] = $total[1000][1001] = $total[1001][1000] = $total[1001][1001] = 0;
$auditory_list = RefBook::find('auditory_memo_1')->getList();
$direction_list = \common\models\guidejob\Direction::getDirectionShortList();

?>

<div class="teachers-schedule">
    <?= $this->render('_search-schedule', compact('model_date')) ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            Расписание занятий: <?php echo RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
        </div>
        <div class="form-group btn-group pull-right">
            <?= Html::a('<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Элементы расписания',
                ['/teachers/default/schedule-items', 'id' => $modelTeachers->id],
                [
                    'title' => 'Открыть в новом окне',
                    'target' => '_blank',
                    'class' => 'btn btn-default',
                ]); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php foreach ($models as $day => $data): ?>
                        <?php
                        $stat[$day][1000][1000] = $stat[$day][1000][1001] = $stat[$day][1001][1000] = $stat[$day][1001][1001] = 0;
                        ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr class="warning">
                                <th class="text-center"
                                    colspan="7"><?= \artsoft\helpers\ArtHelper::getWeekdayValue('name', $day); ?></th>
                            </tr>
                            <tr class="warning">
                                <th class="text-center" style="width: 200px">Время</th>
                                <th class="text-center" style="width: 200px">Нагрузка факт</th>
                                <th class="text-center" style="white-space:nowrap;">Группа/Ученик</th>
                                <th class="text-center">Предмет</th>
                                <th class="text-center">Деятельность</th>
                                <th class="text-center">Кабинет</th>
                            </tr>
                            </thead>
                            <tbody class="container-items">
                            <?php foreach ($data as $index => $items): ?>
                                <?php
                                $time = Schedule::encodeTime($items->time_out) - Schedule::encodeTime($items->time_in);
                                $time = Schedule::astr2academ($time);
                                $stat[$day][$items->subject_type_id][$items->direction_id] = isset($stat[$day][$items->subject_type_id][$items->direction_id]) ? $stat[$day][$items->subject_type_id][$items->direction_id] + $time : $time;
                                $total[$items->subject_type_id][$items->direction_id] = isset($total[$items->subject_type_id][$items->direction_id]) ? $total[$items->subject_type_id][$items->direction_id] + $time : $time;
                                ?>
                                <tr class="<?= $items->subject_type_id == 1001 ? 'info' : '' ?>">
                                    <td style="font-weight: bold">
                                        <?= $items->time_in . ' - ' . $items->time_out ?>
                                    </td>
                                    <td class="<?= $items->direction_id == 1001 ? 'text-right' : '' ?>">
                                        <?= $time; ?>
                                    </td>
                                    <td>
                                        <?= $items->sect_name; ?>
                                    </td>
                                    <td>
                                        <?= $items->subject; ?>
                                    </td>
                                    <td>
                                        <?= $direction_list[$items->direction_id] ?? ''; ?>
                                    </td>
                                    <td>
                                        <?= $auditory_list[$items->auditory_id] ?? ''; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="success">
                                <td colspan="1">Преп/конц - бюджет</td>
                                <td>
                                    <?php echo $stat[$day][1000][1000] . '/' . $stat[$day][1000][1001] ?>
                                </td>
                                <td colspan="4"></td>
                            </tr>
                            <tr class="info">
                                <td colspan="1">Преп/Конц - внебюджет</td>
                                <td>
                                    <?php echo $stat[$day][1001][1000] . '/' . $stat[$day][1001][1001] ?>
                                </td>
                                <td colspan="4"></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                    <table class="table table-bordered">
                        <tbody>
                        <tr class="success">
                            <td style="width: 200px">ИТОГО: Преп/конц - бюджет</td>
                            <td>
                                <?php echo $total[1000][1000] . '/' . $total[1000][1001] ?>
                            </td>
                        </tr>
                        <tr class="info">
                            <td>ИТОГО: Преп/Конц - внебюджет</td>
                            <td>
                                <?php echo $total[1001][1000] . '/' . $total[1001][1001] ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


