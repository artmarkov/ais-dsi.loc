<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\info\Board;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */
/* @var $modelTeachers */

$this->title = Yii::t('art/guide', 'Portfolio');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'title',
        'label' => $model['attributes']['title'],
        'width' => '300px',
        'format' => 'raw',
        'group' => true,
    ],
    [
        'attribute' => 'subject',
        'label' => $model['attributes']['subject'],

        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'sect_name',
        'label' => $model['attributes']['sect_name'],

        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'resume',
        'label' => $model['attributes']['resume'],

    ],
    [
        'attribute' => 'winner_id',
        'label' => $model['attributes']['winner_id'],

    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                    $model['resource'] == 'schoolplan' ? ['/schoolplan/default/view', 'id' => $model['schoolplan_id']] : ['/schoolplan/default/perform', 'id' => $model['schoolplan_id'], 'objectId' => $model['id'], 'mode' => 'view'], [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'target' => '_blank',
                    ]
                );
            },

        ],
    ],


];
?>
<div class="portfolio-index">
    <div class="panel">
        <div class="panel-heading">
            Портфолио преподавателя: <?php echo RefBook::find('teachers_fullname')->getValue($id); ?>
        </div>
        <div class="panel-body">
            <?php echo \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info-circle"></i> Данная таблица формируется за весь учебный год и отражает всю работу преподавателя согласно плану работы и индивидуальных планов учящихся. ',
                'options' => ['class' => 'alert-info'],
            ]);
            ?>
            <?= $this->render('_search', ['model_date' => $model_date]) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => SubjectSect::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?php /*\artsoft\grid\GridPageSize::widget(['pjaxId' => 'portfolio-grid-pjax']) */ ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'portfolio-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'portfolio-grid',
                'pjax' => false,
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => [
                        'attributes' => array_keys($model['attributes']),

                    ],
                    'pagination' => [
                        'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
                    ],
                ]),
                'columns' => $columns,
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

