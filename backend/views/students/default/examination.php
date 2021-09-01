<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\Studyplan;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use lo\widgets\modal\ModalAjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Examinations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-index">

</div>


