<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\EducationCat;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\EducationCatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Education Cats');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-cat-index">
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
//                             echo GridQuickLinks::widget([
//                                'model' => EducationCat::className(),
//                                'searchModel' => $searchModel,
//                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'education-cat-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'education-cat-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'education-cat-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'education-cat-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (EducationCat $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function (EducationCat $model) {
                                    return $model->name;
                                },
                            ],
                            'short_name',
                            'programm_short_name',
                            [
                                'attribute' => 'division_list',
                                'filter' => RefBook::find('division_name')->getList(),
                                'value' => function (EducationCat $model) {
                                    $v = [];
                                    foreach ($model->division_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('division_name')->getValue($id);
                                    }
                                    return implode(';<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [EducationCat::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [EducationCat::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidestudy/education-cat',
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


