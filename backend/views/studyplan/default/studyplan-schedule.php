<?php

use artsoft\helpers\RefBook;

?>
<div class="panel">
    <div class="panel-heading">
        Нагрузка преподавателей и расписание занятий
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Нагрузка преподавателей и расписание занятий
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Дисциплина</th>
                                        <th class="text-center">Группа</th>
                                        <th class="text-center">Преподаватели</th>
                                        <th class="text-center">Расписание занятий</th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                        <tr class="item">
                                            <td>
                                                <?= RefBook::find('subject_memo_2')->getValue($modelSubject->id) ?>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
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
    </div>
</div>