<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\own\Department;
use common\models\guidejob\Bonus;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-index">
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
//                            echo GridQuickLinks::widget([
//                                'model' => Teachers::className(),
//                                'searchModel' => $searchModel,
//                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'teachers-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'teachers-grid-pjax',
                    ])
                    ?>
                    <?= GridView::widget([
                        'id' => 'teachers-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'teachers-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'rowOptions' => function(Teachers $model) {
                            if($model->userStatus == UserCommon::STATUS_ARCHIVE) {
                                return ['class' => 'danger'];
                            }
                            return [];
                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (Teachers $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:1500px'],
                                'attribute' => 'fullName',
                                'controller' => '/teachers/default',

                                'title' => function (Teachers $model) {
                                    return Html::a($model->fullName, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'position_id',
                                'value' => 'position.name',
                                'label' => Yii::t('art/teachers', 'Name Position'),
                                'filter' => \common\models\guidejob\Position::getPositionList(),
                                'options' => ['style' => 'width:350px'],
                            ],
                            [
                                'attribute' => 'department_list',
                                'filter' => Department::getDepartmentList(),
                                'value' => function (Teachers $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Department::findOne($id)->name;
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'bonus_list',
                                'filter' => Bonus::getBonusList(),
                                'value' => function (Teachers $model) {
                                    $v = [];
                                    foreach ($model->bonus_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Bonus::findOne($id)->name;
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'bonus_summ',
                            'tab_num',
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


