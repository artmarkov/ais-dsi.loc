<?php

namespace frontend\controllers;

use artsoft\controllers\BaseController;
use artsoft\helpers\RefBook;
use yii\helpers\ArrayHelper;

class DashboardController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $enableOnlyActions = ['dashboard'];
    public $widgets = NULL;

    public function actions()
    {

        $id = \Yii::$app->user->id;
        $teachers_id = RefBook::find('users_teachers')->getValue($id);
        if ($this->widgets === NULL) {
            $this->widgets = [  
                [
                    [
                        'class' => 'col-md-12',
                        'content' => [
                            'frontend\widgets\dashboard\Quick',
                        ],
                    ],
                    [
                        'class' => 'col-md-8',
                        'content' => [
                           // 'artsoft\user\widgets\dashboard\UsersVisitMap',
                        ],   
                    ],
                    [
                        'class' => 'col-md-4',
                        'content' => [
                            'artsoft\widgets\dashboard\Info',
                            //'artsoft\user\widgets\dashboard\UsersBrowser',
                        ],
                    ],
                ],
                [
                    [
                    'class' => 'col-md-6',
                    'content' => [
//                        'common\widgets\EfficiencyUserBarWidget',
//                        'artsoft\comment\widgets\dashboard\Comments',
                    ],
                ],
                    
                    [
                        'class' => 'col-md-6',
                        'content' => [                          
                            //'artsoft\user\widgets\dashboard\Users',
//                            'artsoft\media\widgets\dashboard\Media',
                        ],
                    ],
                ],
            ];
        }

        return ArrayHelper::merge(parent::actions(), [
            'dashboard' => [
                'class' => 'artsoft\web\DashboardAction',
                'widgets' => $this->widgets,
                //'id' => $teachers_id,
                'id' => $id,
            ]
        ]);
    }
}