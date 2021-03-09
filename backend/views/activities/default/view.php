<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities'), 'url' => ['activities/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-view">

<div class="panel">
    <div class="panel-heading">
        <?=Yii::$app->controller->id ?>
        <?= \artsoft\helpers\ButtonHelper::createButton() ?>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= \artsoft\helpers\ButtonHelper::viewButtons($model) ?>
            </div>
            <div class="panel-body">
                <div class="row">

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'category_id',
                            'auditory_id',
                            'title',
                            'description:ntext',
                            'start_timestamp:datetime',
                            'end_timestamp:datetime',
                            'all_day',
                        ],
                    ])
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
