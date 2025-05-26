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
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?= $this->render('_search-consult', compact('model_date')) ?>
        </div>
    </div>
</div>


