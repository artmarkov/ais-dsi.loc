<?php

use yii\widgets\DetailView;

?>
<div class="student-info">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Карточка ученика:
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $modelStudent,
                        'attributes' => [
                            [
                                'attribute' => 'id',
                                'value' => function ($model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'label' => 'ФЛС'
                            ],
                            [
                                'attribute' => 'fio',
                                'value' => function ($model) {
                                    return $model->fullName;
                                },
                                'label' => 'ФИО'
                            ],
                            [
                                'attribute' => 'birthDate',
                                'value' => function ($model) {
                                    return $model->userBirthDate;
                                },
                                'label' => 'Дата рождения'
                            ],
                            [
                                'attribute' => 'age',
                                'value' => function ($model) {
                                    $age = \artsoft\helpers\ArtHelper::age(Yii::$app->formatter->asTimestamp($model->userBirthDate)); // полных лет на начало обучения
                                    return $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.';
                                },
                                'label' => 'Возраст'
                            ],
                            [
                                'attribute' => 'phone',
                                'value' => function ($model) {
                                    return $model->userPhone;
                                },
                                'label' => 'Телефон'
                            ],
                            [
                                'attribute' => 'email',
                                'value' => function ($model) {
                                    return $model->userEmail;
                                },
                                'label' => 'Е-майл'
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Родители и официальные представители:
                </div>
                <div class="panel-body">
                    <?php
                    foreach ($modelStudent->studentDependence as $m) {
                        $modelParent = \common\models\parents\Parents::findOne($m->parent_id);
                        echo DetailView::widget([
                            'model' => $modelParent,
                            'attributes' => [
                                [
                                    'attribute' => 'fio',
                                    'value' => function ($model) {
                                        return $model->fullName;
                                    },
                                    'label' => 'ФИО'
                                ],
                                [
                                    'attribute' => 'relation',
                                    'value' => function ($model) use ($m) {
                                        return $m->userRelation ? $m->userRelation->name : '';
                                    },
                                    'label' => 'Отношения'
                                ],
                                /* [
                                     'attribute' => 'birthDate',
                                     'value' => function ($model) {
                                         return $model->userBirthDate;
                                     },
                                     'label' => 'Дата рождения'
                                 ],
                                 [
                                     'attribute' => 'age',
                                     'value' => function ($model) {
                                         $age = \artsoft\helpers\ArtHelper::age(Yii::$app->formatter->asTimestamp($model->userBirthDate)); // полных лет на начало обучения
                                         return $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.';
                                     },
                                     'label' => 'Возраст'
                                 ],*/
                                [
                                    'attribute' => 'phone',
                                    'value' => function ($model) {
                                        return $model->userPhone;
                                    },
                                    'label' => 'Телефон'
                                ],
                                [
                                    'attribute' => 'email',
                                    'value' => function ($model) {
                                        return $model->userEmail;
                                    },
                                    'label' => 'Е-майл'
                                ],
                            ],
                        ]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>