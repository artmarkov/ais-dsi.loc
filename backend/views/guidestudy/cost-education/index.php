<?php

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\CostEducation;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Cost Education');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-education-index">
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
                        /*'bulkActionOptions' => [
                            'gridId' => 'education-level-grid',
                        ],*/
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (CostEducation $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'programm_id',
                                'value' => function (CostEducation $model) {
                                    return RefBook::find('education_programm_short_name')->getValue($model->programm_id) ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'standard_basic',
                                'value' => function (CostEducation $model) {
                                    return Editable::widget([
                                        'buttonsTemplate' => "{reset}{submit}",
                                        'name' => 'standard_basic',
                                        'asPopover' => true,
                                        'value' => $model->standard_basic,
                                        'header' => 'Введите Норматив базовый, руб.',
                                        'format' => Editable::FORMAT_LINK,
                                        'inputType' => Editable::INPUT_TEXT,
                                        'size' => 'md',
                                        'formOptions' => [
                                            'action' => Url::toRoute(['/guidestudy/cost-education/set-standart-basic', 'id' => $model->id]),
                                        ],
                                    ]);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'standard_basic_ratio',
                                'value' => function (CostEducation $model) {
                                    return Editable::widget([
                                        'buttonsTemplate' => "{reset}{submit}",
                                        'name' => 'standard_basic_ratio',
                                        'asPopover' => true,
                                        'value' => $model->standard_basic_ratio,
                                        'header' => 'Введите Коэффициент к базовому нормативу',
                                        'format' => Editable::FORMAT_LINK,
                                        'inputType' => Editable::INPUT_TEXT,
                                        'size' => 'md',
                                        'formOptions' => [
                                            'action' => Url::toRoute(['/guidestudy/cost-education/set-basic-ratio', 'id' => $model->id]),
                                        ],
                                    ]);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'standard',
                                'value' => function (CostEducation $model) {
                                    return $model->getStandard();
                                },
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidestudy/cost-education',
                                'template' => '{update}',
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


