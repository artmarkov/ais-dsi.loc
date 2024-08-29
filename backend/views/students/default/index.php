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
            <?= Html::a(
                '<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art/student', 'Student Registration'),
                ['students/default/finding'],
                [
                    'class' => 'btn btn-success btn-md' ,
                    'name' => 'submitAction',
                    'value' => 'save',
                ]
            );
            ?>
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
                        /*'bulkActionOptions' => [
                            'gridId' => 'student-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],*/
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'options' => ['style' => 'width:30px'],
                                'value' => function (Student $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'fullName',
                                'value' => function (Student $model) {
                                    return $model->fullName;
                                },
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
                                'attribute' => 'limited_status_list',
                                'filter' => \common\models\students\Student::getLimitedStatusList(),
                                'value' => function (Student $model) {
                                    $v = [];
                                    foreach ($model->limited_status_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = \common\models\students\Student::getLimitedStatusValue($id);
                                    }
                                    return implode(',<br/> ', $v);
                                },
                                'options' => ['style' => 'width:150px'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'userStatus',
                                'optionsArray' => [
                                    [UserCommon::STATUS_ACTIVE, Yii::t('art', 'Active'), 'info'],
                                    [UserCommon::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'danger'],
                                ],
                                'options' => ['style' => 'width:120px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/students/default',
                                'template' => '{view} {update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],

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


