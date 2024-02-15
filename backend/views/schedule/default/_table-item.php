<table class="table table-striped kv-grid-table table-hover table-bordered">
    <thead class="bg-info">
    <tr>
        <th class="text-center" style="min-width: 100px; white-space: nowrap">Время</th>
        <th class="text-center" style="min-width: 100px; white-space: nowrap">Группа/уч-к</th>
        <th class="text-center" style="min-width: 100px; white-space: nowrap">Предмет</th>
        <th class="text-center" style="min-width: 100px; white-space: nowrap">Преп-ль</th>
    </tr>
    </thead>
    <tbody>
    <?php use artsoft\helpers\Html;
    use artsoft\helpers\Schedule;
//    echo '<pre>' . print_r($dataItem, true) . '</pre>'; die();
    foreach ($dataItem as $time_in => $data_times):
        foreach ($data_times as $item => $val):
            if ($item == 0):
                ?>
                <tr class="item">
                <td><?= $val['week_num'] != 0 ? $val['week_num'] . ' нед. ' . Schedule::decodeTime($val['time_in']) : Schedule::decodeTime($val['time_in']) ?>
                    <?= '-' . Schedule::decodeTime($val['time_out']) ?>
                </td>
                <td><?= $val['sect_name'] ?></td>
                <td><?= $val['subject'] ?></td>
                <td>
            <?php endif; ?>
            <?= \artsoft\Art::isBackend() ? Html::a($teachers_list[$val['teachers_id']],
            ['/teachers/default/schedule-items', 'id' => $val['teachers_id']], ['target' => '_blank', /*'class' => 'btn btn-info'*/])
            : $teachers_list[$val['teachers_id']]; ?>
            <br/>
        <?php endforeach; ?>
            </td>
            </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr class="bg-warning text-left">
        <td colspan="4"> <?= $weekDay . ' - ' . $auditory->num; ?></td>
    </tr>
    </tfoot>
</table>