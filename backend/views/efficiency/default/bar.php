<?php

use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = 'График';
?>
<?php usort($data['data'], function ($a, $b) {
    return $b['total'] <=> $a['total'];
});
?>
<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">
            График эффективности
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="panel">
                <div class="panel-body">

                    <?= ChartJs::widget([
                        'type' => 'bar',
                        'data' => [
                            'labels' => array_column($data['data'], 'name'),
                            'datasets' => [
                                [
                                    'label' => "Бонусы за период",
                                    'backgroundColor' => "rgba(255,0,0,0.9)",
                                    'data' => array_column($data['data'], 'total'),
                                ]
                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

