<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('<?= $generator->messageCategory ?>', <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= "<?= " ?> Html::encode($this->title) ?></h3>            
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?= "<?= " ?>
                Html::a(Yii::t('art', 'Edit'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary'])
                ?>
                <?= "<?= " ?>
                Html::a(Yii::t('art', 'Delete'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/delete', 'id' => $model->id],
                    [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?= "<?= " ?>
                Html::a(Yii::t('art', 'Add New'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/create'],
                    ['class' => 'btn btn-sm btn-success pull-right'])
                ?>
            </p>


            <?= "<?= " ?>
            DetailView::widget([
                'model' => $model,
                'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
                ],
            ])
            ?>

        </div>
    </div>

</div>
