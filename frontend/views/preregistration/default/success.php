<?php

/**
 * @var yii\web\View $this
 * @var $model \common\models\education\EntrantPreregistrations
 * @var $message_1
 * @var $message_2
 */

$this->title = 'Успешное выполнение';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="success">
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $message_1 ?>
    </div>
    <?php if ($message_2 != ''): ?>
        <div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $message_2 ?>
        </div>
    <?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Для информации:
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    Вы записали ребенка на программу обучения: <?= strip_tags(\common\models\education\EntrantProgramm::getEntrantProgrammValue($model->entrant_programm_id)) . '.'; ?>
                    Просьба написать по телефону в whats app или Telegram 8-926-350-17-97 с 10:00 - 18:00 с понедельника по пятницу для уточнения информации по обучению и оплате.
                </div>
                <div class="col-sm-12">
                    Если Вы ошибочно записали ребенка на программу или желаете записать ребенка на 2 или более программ, также свяжитесь по телефону, указанному выше(система позволяет зарегистрироваться только 1 раз).
                </div>
            </div>
        </div>
    </div>
</div>
