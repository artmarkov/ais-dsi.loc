<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Measure */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Measures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="measure-view">

    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?=                 Html::a('Edit', ['/measure/update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary'])
                ?>
                <?=                 Html::a('Delete', ['/measure/delete', 'id' => $model->id],
                    [
                    'class' => 'btn btn-sm btn-default',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?=                 Html::a(Yii::t('art', 'Add New'), ['/measure/create'],
                    ['class' => 'btn btn-sm btn-primary pull-right'])
                ?>
            </p>


            <?

            /*$items = array();
            foreach ($model->getEavAttributes() as $attr)
            {
                $items[] = array(
                    'label'=>$model->getEavAttribute($attr)->label,
                    'value'=>$model->owner->$attr,
                    'visible'=> $model->owner->$attr,
                );
               // echo '<pre>' . print_r($model->getEavAttribute($attr), true) . '</pre>';
            }*/
            ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => ArrayHelper::merge(
                    [
                        'id',
                        'name',
                        'abbr',
                    ],
                    $model->getEavAttributesViewList($model)
                ),
            ])
            ?>

        </div>
    </div>

</div>
