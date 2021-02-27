<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subject\Subject;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subject\search\SubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide','Subjects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/subject/default/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    echo GridQuickLinks::widget([
                        'model' => Subject::className(),
                        'searchModel' => $searchModel,
                    ])
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'subject-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'subject-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'subject-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'subject-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'name',
                        'controller' => '/subject/default',
                        'title' => function (Subject $model) {
                            return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {delete}',
                    ],
                    'slug',
                    [
                        'attribute' => 'gridCategorySearch',
                        'filter' => Subject::getSubjectCategoryList(),
                        'value' => function (Subject $model) {
                            return implode(', ',
                                ArrayHelper::map($model->subjectCategoryItem, 'id', 'name'));
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'gridDepartmentSearch',
                        'filter' => Subject::getDepartmentList(),
                        'value' => function (Subject $model) {
                            return implode(', ',
                                ArrayHelper::map($model->departmentItem, 'id', 'name'));
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Subject::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [Subject::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],

                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


