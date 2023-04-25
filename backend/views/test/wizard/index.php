<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use backend\models\Customer;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use buttflattery\formwizard\FormWizard;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

echo FormWizard::widget([
//    'formOptions'=>[
//        'id'=>'my_form_ajax',
//        'enableClientValidation'=>false,
//        'enableAjaxValidation'=>true,
//    ],
    'theme' => FormWizard::THEME_ARROWS,
//    'transitionEffect' => 'none',
//    'showStepURLhash' => true,
//    'toolbarPosition' => 'both',
    'labelNext' => 'Вперед',
    'labelPrev' => 'Назад',
    'labelFinish' => 'Отправить',
    'steps' => [
        [
            'fieldConfig' => [
                'name' => [
                    'template' => "<div class=\"col-sm-3\">{label}</div><div class=\"col-sm-9\">\n{input}\n{error}\n{hint}\n</div>",
                ],
                'slug' => [
                    'template' => "<div class=\"col-sm-3\">{label}</div><div class=\"col-sm-9\">\n{input}\n{error}\n{hint}\n</div>",
                ],
//                'address' => [
//                    'template' => "<div class=\"col-sm-3\">{label}</div><div class=\"col-sm-9\">\n{input}\n{error}\n{hint}\n</div>",
//                ],
            ],
            'model' => $shootsModel,
            'title' => 'Данные 1',
            'description' => 'Добавление 1',
            'formInfoText' => 'Заполните все поля формы 1'
        ],
        [
            'model' => $shootTagModel,
//            'isSkipable'=> true,
            'title' => 'Дапнные 2',
            'description' => 'Добавление 2',
            'formInfoText' => 'Заполните все поля формы 2'
        ],
    ]
]);
?>



