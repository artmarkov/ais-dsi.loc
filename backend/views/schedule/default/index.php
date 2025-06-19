<?php

use artsoft\helpers\RefBook;

$teachers_list = RefBook::find('teachers_fio')->getList();
?>

<div class="schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Расписание занятий:
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive kv-grid-container">
                        <table class="table table-striped kv-grid-table table-hover table-bordered">
                            <thead class="bg-primary">
                            <tr>
                                <th class="text-center" style="min-width: 100px; white-space: nowrap">Аудитория</th>
                                <?php foreach (\artsoft\helpers\ArtHelper::getWeekdayList() as $item => $weekDay): ?>
                                    <th class="text-center" style="min-width: 100px"><?= $weekDay ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
//                            echo '<pre>' . print_r($data, true) . '</pre>';
                            ?>
                            <?php foreach ($modelsAuditory as $index => $auditory): ?>
                                <tr class="item">
                                    <td style="min-width: 100px; font-weight: bold"><?= $auditory->num . ' ' .  $auditory->name?></td>
                                    <?php foreach (\artsoft\helpers\ArtHelper::getWeekdayList() as $item => $weekDay): ?>
                                        <td class="text-center" style="min-width: 100px">
                                            <?php if (isset($data[$auditory->id][$item])): ?>
                                                <?= $this->render('_table-item', ['dataItem' => $data[$auditory->id][$item], 'weekDay' => $weekDay, 'auditory' => $auditory, 'teachers_list' => $teachers_list]) ?>
                                            <?php endif; ?>

                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
