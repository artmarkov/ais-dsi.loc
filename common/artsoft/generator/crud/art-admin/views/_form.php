<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \artsoft\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use artsoft\widgets\ActiveForm;
use <?= $generator->modelClass ?>;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php \n" ?>
    $form = ActiveForm::begin([
            'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= "<?= " ?> Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                    <?php foreach ($generator->getColumnNames() as $attribute) {
                        if (in_array($attribute, $safeAttributes)) {
                            echo "\n                    <?= " . $generator->generateActiveField($attribute) . " ?>\n";
                        }
                    } ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= "<?= " ?>Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/index'], ['class' => 'btn btn-default']) ?>
                        <?= "<?= " ?>Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?= "<?php " ?> if (!$model->isNewRecord): ?>
                            <?= "<?= " ?>Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        <?= "<?php " ?>endif; ?>
                    </div>
                    <?= "<?= " ?> \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?= "<?php " ?> ActiveForm::end(); ?>

</div>
