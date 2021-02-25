<?php

namespace artsoft\controllers\admin;

use yii\helpers\ArrayHelper;

class DashboardController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $enableOnlyActions = ['index'];
    public $widgets = NULL;

    public function actions()
    {

        if ($this->widgets === NULL) {
            $this->widgets = [  
                [
                    [
                        'class' => 'col-md-8',
                        'content' => [
                            'artsoft\user\widgets\dashboard\UsersVisitMap',
                        ],   
                    ],
                    [
                        'class' => 'col-md-4',
                        'content' => [
                            'artsoft\widgets\dashboard\Info',
                            'artsoft\user\widgets\dashboard\UsersBrowser',
                        ],
                    ],
                ],
                [
                    [
                    'class' => 'col-md-6',
                    'content' => [
//                        'artsoft\post\widgets\dashboard\Posts',
//                        'artsoft\comment\widgets\dashboard\Comments',
                    ],
                ],
                    
                    [
                        'class' => 'col-md-6',
                        'content' => [                          
                            'artsoft\user\widgets\dashboard\Users',
//                            'artsoft\media\widgets\dashboard\Media',
                        ],
                    ],
                ],
            ];
        }

        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => 'artsoft\web\DashboardAction',
                'widgets' => $this->widgets,
            ]
        ]);
    }
}