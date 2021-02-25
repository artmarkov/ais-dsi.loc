<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$modelClass = StringHelper::basename($generator->modelClass);
$modelClassId  = Inflector::camel2id(StringHelper::basename($generator->modelClass));
 
echo "<?php\n";
?>

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use <?= $generator->modelClass ?>;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('<?= $generator->messageCategory ?>', <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= $modelClassId ?>-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= "<?= " ?> Html::encode($this->title) ?></h3>
            <?= "<?= " ?>Html::a(Yii::t('art', 'Add New'), ['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default/create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?= "<?php \n" ?>
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => <?= $modelClass ?>::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= "<?= " ?> GridPageSize::widget(['pjaxId' => '<?= $modelClassId ?>-grid-pjax']) ?>
                </div>
            </div>

            <?= "<?php \n" ?>
            Pjax::begin([
                'id' => '<?= $modelClassId ?>-grid-pjax',
            ])
            ?>

            <?= "<?= \n" ?>
            GridView::widget([
                'id' => '<?= $modelClassId ?>-grid',
                'dataProvider' => $dataProvider,
                <?= !empty($generator->searchModelClass) ? '\'filterModel\' => $searchModel,'.PHP_EOL : ''?>
                'bulkActionOptions' => [
                    'gridId' => '<?= $modelClassId ?>-grid',
                    'actions' => [ Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/default',
                        'title' => function(<?= $modelClass ?> $model) {
                            return Html::a($model->id, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {view} {delete}',
                    ],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

                ],
            ]);
            ?>

            <?= "<?php" ?> Pjax::end() ?>
        </div>
    </div>
</div>


