<?php

use artsoft\helpers\RefBook;
use yii\widgets\DetailView;

?>
<div class="progress-modal">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'test_name',
            'lesson_topic',
            'lesson_rem',
            'mark_label',
            [
                'attribute' => 'lesson_date',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->lesson_date);
                },
            ],
        [
                'attribute' => 'teachers_list',
                'value' => function ($model) {
                    $v = [];
                    foreach (explode(',',$model->teachers_list) as $id) {
                        $v[] = $id != null ? RefBook::find('teachers_fio')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                },
            'label' => 'Преподаватель'
            ],
        ],
    ]);
    ?>

</div>