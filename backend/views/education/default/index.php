<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use common\models\education\EducationSpeciality;
use common\models\education\EducationProgramm;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\EducationProgrammSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Education Programms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-programm-index">
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
                                'model' => EducationProgramm::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'education-programm-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'education-programm-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'education-programm-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'education-programm-grid',
                            'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/education/default',
                                'title' => function (EducationProgramm $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'education_cat_id',
                                'filter' => RefBook::find('education_cat_short')->getList(),
                                'value' => function (EducationProgramm $model) {
                                    return RefBook::find('education_cat_short')->getValue($model->education_cat_id);
                                },
                            ],

                            'name',
                            [
                                'attribute' => 'speciality_list',
                                'filter' => RefBook::find('education_speciality')->getList(),
                                'value' => function (EducationProgramm $model) {
                                    $v = [];
                                    foreach ($model->speciality_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('education_speciality')->getValue($id);
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'description',
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


