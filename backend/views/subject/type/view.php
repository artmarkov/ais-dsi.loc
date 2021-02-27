<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subject\SubjectType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Subject Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-type-view">

    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?=                 Html::a('Edit', ['/subject/type/update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary'])
                ?>
                <?=                 Html::a('Delete', ['/subject/type/delete', 'id' => $model->id],
                    [
                    'class' => 'btn btn-sm btn-default',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?=                 Html::a(Yii::t('art', 'Add New'), ['/subject/type/create'],
                    ['class' => 'btn btn-sm btn-primary pull-right'])
                ?>
            </p>


            <?=             DetailView::widget([
                'model' => $model,
                'attributes' => [
            'id',
            'name',
            'slug',
            'status',
                ],
            ])
            ?>

        </div>
    </div>

</div>
