<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use yii\helpers\Url;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="protocol">
    <div class="panel">
        <div class="panel-heading">
            Результаты испытаний
            <?= $this->render('_search_protocol', compact('model', 'model_date')) ?>
        </div>
        <div class="panel-body">

        </div>
    </div>
</div>


