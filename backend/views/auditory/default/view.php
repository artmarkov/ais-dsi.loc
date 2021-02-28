<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Auditory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Auditory'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditory-view">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        <?= Html::a('Edit', ['/auditory/default/update', 'id' => $model->id],
                            ['class' => 'btn btn-sm btn-primary'])
                        ?>
                        <?= Html::a('Delete', ['/auditory/default/delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-sm btn-default',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                        ?>
                        <?= Html::a(Yii::t('art', 'Add New'), ['/auditory/default/create'],
                            ['class' => 'btn btn-sm btn-primary pull-right'])
                        ?>
                    </p>

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'building_id',
                            'cat_id',
                            'study_flag',
                            'num',
                            'name',
                            'floor',
                            'area',
                            'capacity',
                            'description',
                            'order',
                        ],
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
