<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\students\Student;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/student', 'Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => Student::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'student-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'student-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'student-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'student-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (Student $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'studentsFullName',
                                'controller' => '/students/default',
                                'title' => function (Student $model) {
                                    return Html::a($model->studentsFullName, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],

                            [
                                'options' => ['style' => 'width:200px'],
                                'attribute' => 'position_id',
                                'value' => 'position.name',
                                'label' => Yii::t('art/student', 'Position'),
                                'filter' => common\models\students\StudentPosition::getPositionList(),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\DateFilterColumn',
                                'attribute' => 'userBirthDate',
                                'value' => function (Student $model) {
                                    return '<span style="font-size:85%;" class="label label-default">'
                                        . $model->userBirthDate . '</span>';
                                },
                                'label' => Yii::t('art', 'Birth Date'),
                                'format' => 'raw',
                                'options' => ['style' => 'width:150px'],
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'userStatus',
                                'optionsArray' => [
                                    [UserCommon::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [UserCommon::STATUS_ARCHIVE, Yii::t('art', 'Archive'), 'danger'],
                                ],
                                'options' => ['style' => 'width:120px']
                            ],

                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


