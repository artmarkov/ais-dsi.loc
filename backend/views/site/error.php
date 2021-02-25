<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-error">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">

                <div class="alert alert-danger alert-dismissible">
                    <i class="fa fa-frown-o"></i> 
                    <?= nl2br(Html::encode($message)) ?>
                </div>

                <div class="e404"><?= nl2br(Html::encode($code)) ?></div> 
            <p>
                The above error occurred while the Web server was processing your request.
            </p>

            <p>
                Please contact us if you think this is a server error. Thank you.
            </p>
            </div>
        </div>
    </div>
</div>

</div>
