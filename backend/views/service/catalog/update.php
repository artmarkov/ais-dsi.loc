<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\Catalog */

$this->title = Yii::t('art/service', 'Update Catalog: ' . $model->name, [
    'nameAttribute' => '' . $model->name,
]);
?>
<div class="catalog-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
