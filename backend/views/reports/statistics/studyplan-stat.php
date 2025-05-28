<?php

use artsoft\grid\GridView;
use artsoft\helpers\Schedule;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model_date  */
/* @var $data  */

$this->title = 'Статистика по учебной работе';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="studyplan-stat">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <?= $this->title ?>
                </div>
                <div class="panel-body">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info-circle"></i> Test',
                        'options' => ['class' => 'alert-success'],
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr class="warning">
                                    <th class="text-center" style="width: 200px">Форма занятий</th>
                                    <th class="text-center" style="width: 200px">Отчисленные за <?=$model_date->plan_year?>/<?=$model_date->plan_year + 1?> учебный год</th>
                                    <th class="text-center" style="width: 200px">Отчисленные c 01.01.<?=$model_date->plan_year + 1?>-31.05.<?=$model_date->plan_year + 1?></th>
                                </tr>
                                </thead>
                                <tbody class="container-items">
                                <?php foreach ($data[2] as $index => $items): ?>
                                    <tr>
                                        <td>
                                            <?= $items['name'] ?? ''; ?>
                                        </td>
                                        <td>
                                            <?= $items['qty']; ?>
                                        </td>
                                        <td>
                                            <?= $data[1][$index]['qty'] ?? ''; ?>
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
