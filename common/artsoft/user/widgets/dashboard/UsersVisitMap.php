<?php

namespace artsoft\user\widgets\dashboard;

use artsoft\models\User;
use artsoft\models\UserVisitLog;
use artsoft\user\models\search\UserSearch;
use artsoft\widgets\DashboardWidget;
use Yii;

class UsersVisitMap extends DashboardWidget
{ 
    /**
     * Post index action
     */
    public $indexAction = 'user/default/index';

    public function run()
    {
      
        if (User::hasPermission('viewUsers')) {

            $visits = UserVisitLog::find()->all();
           
            foreach ($visits as $visit) {
//                 echo '<pre>' . print_r($visit->geoLocation, true) . '</pre>';
                if (!empty($visit->geoLocation['country']['id']))
                {
                    $values[] = $visit->geoLocation['country']['iso'];
                }
                $sity_id = $visit->geoLocation['city']['id'];
                if (!empty($sity_id))
                {
                    $markers_id[] = $sity_id;
                    $markers_coord[$sity_id] = [
                            $visit->geoLocation['city']['lat'],
                            $visit->geoLocation['city']['lon']
                        ];
                    $markers_name[$sity_id] = empty($visit->geoLocation['city']['name_en']) ? $visit->geoLocation['country']['name_en'] : $visit->geoLocation['city']['name_en'];
                }
            }
            
            if(empty($markers_id)) {
                return;
            }
            
            foreach (array_count_values($markers_id) as $sity_id => $count) {
                $markers[] = [
                    'latLng' => $markers_coord[$sity_id],
                    'name' => $markers_name[$sity_id] . ' - ' . $count . ' visits',
                ];
            }
            return $this->render('users-visit-map', [
                'values' => array_count_values($values),
                'markers' => $markers,
            ]);

        }
    }

}