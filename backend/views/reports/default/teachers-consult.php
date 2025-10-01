<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;

$stat = [];
$auditory_list = RefBook::find('auditory_memo_1')->getList();
$direction_list = \common\models\guidejob\Direction::getDirectionShortList();
//echo '<pre>' . print_r($models, true) . '</pre>'; die();
?>

<div class="teachers-schedule">
    <?= $this->render('_search-consult', compact('model_date')) ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            Расписание консультаций: <?php echo RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
        </div>
        <div class="form-group btn-group pull-right">
            <?= Html::a('<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Расписание консультаций',
                ['/teachers/default/consult-items', 'id' => $modelTeachers->id],
                [
                    'title' => 'Открыть в новом окне',
                    'target' => '_blank',
                    'class' => 'btn btn-default',
                ]); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    $stat[1000][1000] = $stat[1000][1001] = $stat[1001][1000] = $stat[1001][1001] = 0;
                    ?>
                    <table class="table table-bordered table-striped">
                        <thead>

                        <tr class="warning">
                            <th class="text-center" style="width: 200px">Дата и время</th>
                            <th class="text-center" style="width: 200px">Нагрузка факт</th>
                            <th class="text-center" style="white-space:nowrap;">Группа/Ученик</th>
                            <th class="text-center">Предмет</th>
                            <th class="text-center">Деятельность</th>
                            <th class="text-center">Кабинет</th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($models as $index => $items): ?>
                            <?php
                            $time = Yii::$app->formatter->asTimestamp($items->datetime_out) - Yii::$app->formatter->asTimestamp($items->datetime_in);
                            $time = Schedule::astr2academ($time);
                            $array = explode(' ', $items->datetime_out);
                            $datetime = $items->datetime_in . ' - ' . $array[1];
                            $stat[$items->subject_type_id][$items->direction_id] = isset($stat[$items->subject_type_id][$items->direction_id]) ? $stat[$items->subject_type_id][$items->direction_id] + $time : $time;
                            ?>
                            <tr class="<?= $items->subject_type_id == 1001 ? 'info' : '' ?>">
                                <td style="font-weight: bold">
                                    <?= $datetime ?>
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
                                <?php echo $stat[1000][1000] . '/' . $stat[1000][1001] ?>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                        <tr class="info">
                            <td colspan="1">Преп/Конц - внебюджет</td>
                            <td>
                                <?php echo $stat[1001][1000] . '/' . $stat[1001][1001] ?>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


