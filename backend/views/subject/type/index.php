<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subject\SubjectType;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subjects'), 'url' => ['subject/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-type-index">
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
                                'model' => SubjectType::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <? /*=  GridPageSize::widget(['pjaxId' => 'subject-type-grid-pjax'])*/ ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-type-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'subject-type-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'subject-type-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'controller' => '/subject/type',
                                'title' => function (SubjectType $model) {
                                    return Html::a($model->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {delete}',
                            ],

                            'slug',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [SubjectType::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [SubjectType::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
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
    </div>
</div>


