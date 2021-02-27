<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\Book */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-view">

    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?=                 Html::a('Edit', ['/book/default/update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary'])
                ?>
                <?=                 Html::a('Delete', ['/book/default/delete', 'id' => $model->id],
                    [
                    'class' => 'btn btn-sm btn-default',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?=                 Html::a(Yii::t('art', 'Add New'), ['/book/default/create'],
                    ['class' => 'btn btn-sm btn-primary pull-right'])
                ?>
            </p>


            <?=             DetailView::widget([
                'model' => $model,
                'attributes' => [
            'id',
            'name',
            'buy_amount',
                ],
            ])
            ?>

        </div>
    </div>

</div>
