<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\EducationLevel;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Education Levels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-level-index">
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
                                'model' => EducationLevel::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'education-level-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'education-level-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'education-level-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'education-level-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (EducationLevel $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function (EducationLevel $model) {
                                    return $model->name;
                                },
                            ],
                            'short_name',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [EducationLevel::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [EducationLevel::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidestudy/education-level',
                                'template' => '{update} {delete}',
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


