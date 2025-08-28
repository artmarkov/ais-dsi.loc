<?php

/**
 * @var yii\web\View $this
 * @var $message
 */

$this->title = 'Ошибка';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="danger">
    <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $message ?>
    </div>
</div>
