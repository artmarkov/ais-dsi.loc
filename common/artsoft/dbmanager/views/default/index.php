<?php

use yii\helpers\Html;
use artsoft\grid\GridView;
use yii\helpers\Url;
use artsoft\grid\GridPageSize;
use yii\widgets\Pjax;

$this->title = Yii::t('art/dbmanager', 'DB Manager');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="dbmanager-index">

    <div class="panel">
        <div class="panel-heading">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art/dbmanager', 'Create Dump'), ['/dbmanager/default/export'], ['class' => 'btn btn-sm btn-success']) ?>

            <?= Html::a(Yii::t('art/dbmanager', 'Delete All Dump'), ['/dbmanager/default/delete-all'], [
                'title' => Yii::t('art/dbmanager', 'Delete All Dump'),
                'data-method' => 'post',
                'data-confirm' => Yii::t('art/dbmanager', 'All database entries will be deleted. Are you sure?'),
                'class' => 'btn btn-sm btn-danger',
            ]) ?>
        </div>
        <div class="panel-body">
        <div class="row">

                <div class="col-sm-12 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'dbmanager-grid-pjax']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
            <?php 
            Pjax::begin([
                'id' => 'dbmanager-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '<div class="table-responsive">{items}</div>'
                                . '<div class="row">'
                                . '<div class="col-xs-4 col-md-3"></div>'
                                . '<div class="col-xs-8 col-md-9 text-right">{summary}</div>'
                                . '</div>'
                                . '<div class="row">'
                                . '<div class="col-xs-12 text-center">{pager}</div>'
                                . '</div>',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px']],
                    [
                        'options' => ['style' => 'width:70%'],
                        'attribute' => 'dump',
                        'value' => function($data) {
                            return Html::encode($data['dump']);
                        },
                        'label' => Yii::t('art/dbmanager', 'Dump'),

                    ],                   
                    [
                        'attribute' => 'size',
                        'format' => 'text',
                        'label' => Yii::t('art/dbmanager', 'Size'),
                    ],
                    [
                        'attribute' => 'create_at',
                        'format' => 'text',
                        'label' => Yii::t('art/dbmanager', 'Create time'),
                    ],
                    [
                        'options' => ['style' => 'width:20px'],
                        'format' => 'raw',
                        'value' => function ($data, $id) {
                            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>',
                                Url::to(['/dbmanager/default/download', 'path' => $data['dump']]),
                                [
                                    'title' => Yii::t('art/dbmanager', 'Download'),
                                    'class' => 'btn btn-sm btn-info',
                                    'data-pjax' => '0'
                                ]);
                        },
                    ],
                    [
                        'options' => ['style' => 'width:20px'],
                        'format' => 'raw',
                        'value' => function ($data, $id) {
                            return Html::a('<span class="glyphicon glyphicon-import"></span>',
                                Url::to(['/dbmanager/default/import', 'path' => $data['dump']]),
                                [
                                    'title' => Yii::t('art/dbmanager', 'Import'),
                                    'data-confirm' => Yii::t('art/dbmanager', 'All database entries will be overwritten. Are you sure?'),
                                    'class' => 'btn btn-sm btn-warning',
                                ]);
                        },
                    ],
                    [
                        'options' => ['style' => 'width:20px'],
                        'format' => 'raw',
                        'value' => function ($data, $id) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                Url::to(['/dbmanager/default/delete', 'path' => $data['dump']]),

                                [
                                    'title' => Yii::t('art', 'Delete'),
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('art/dbmanager', 'The database dump will be deleted. Are you sure?'),
                                    'class' => 'btn btn-sm btn-danger',
                                ]);
                        },
                    ],
                ],
            ]); ?>

            <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
