<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers','Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/teachers/default/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php 
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Teachers::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'teachers-grid-pjax']) ?>
                </div>
            </div>

            <?php 
            Pjax::begin([
                'id' => 'teachers-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'teachers-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'teachers-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'options' => ['style' => 'width:300px'],
                        'attribute' => 'teachersFullName',
                        'controller' => '/teachers/default',
                        'title' => function(Teachers $model) {
                    return Html::a($model->teachersFullName, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                },
                        'buttonsTemplate' => '{update} {delete}',
                    ],
                    [
                        'attribute' => 'position_id',
                        'value' => 'position.name',
                        'label' => Yii::t('art/teachers', 'Name Position'),
                        'filter' => common\models\teachers\Position::getPositionList(),
                    ],
                    [
                        'attribute' => 'work_id',
                        'value' => 'work.name',
                        'label' => Yii::t('art/teachers', 'Name Work'),
                        'filter' => common\models\teachers\Work::getWorkList(),
                    ],
                    [
                        'attribute' => 'gridDepartmentSearch',
                        'filter' => Teachers::getDepartmentList(),
                        'value' => function (Teachers $model) {
                            return implode(', ',
                                ArrayHelper::map($model->departmentItem, 'id', 'name'));
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'user.phone',
                    'user.email',
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


