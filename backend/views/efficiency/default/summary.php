<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\efficiency\TeachersEfficiency;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\efficiency\search\TeachersEfficiencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Efficiencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">

        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
<?php echo '<pre>' . print_r($tree, true) . '</pre>';?>
<?php echo '<pre>' . print_r($models, true) . '</pre>';?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
