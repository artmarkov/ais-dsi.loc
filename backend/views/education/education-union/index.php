<?php

use artsoft\helpers\RefBook;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\EducationUnion;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\EducationUnionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Education Unions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-union-index">
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
                             echo GridQuickLinks::widget([
                                'model' => EducationUnion::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'education-union-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'education-union-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'education-union-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'education-union-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/guidestudy/education-union',
                                'title' => function (EducationUnion $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            'union_name',
                            'class_index',
                            [
                                'attribute' => 'term_mastering',
                                'filter' => \artsoft\helpers\ArtHelper::getTermList(),
                                'value' => function (EducationUnion $model) {
                                    return \artsoft\helpers\ArtHelper::getTermList()[$model->term_mastering];
                                },
                            ],
                            [
                                'attribute' => 'programm_list',
                                'filter' => RefBook::find('education_programm_short_name', \common\models\education\EducationProgramm::STATUS_ACTIVE)->getList(),
                                'value' => function (EducationUnion $model) {
                                    $v = [];
                                    foreach ($model->programm_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('education_programm_short_name', \common\models\education\EducationProgramm::STATUS_ACTIVE)->getValue($id);
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [EducationUnion::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [EducationUnion::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:60px']
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


