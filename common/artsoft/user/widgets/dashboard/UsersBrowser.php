<?php

namespace artsoft\user\widgets\dashboard;

use artsoft\models\User;
use artsoft\models\UserVisitLog;
use artsoft\widgets\DashboardWidget;
use Yii;

class UsersBrowser extends DashboardWidget
{
   
    /**
     * Post index action
     */
    public $indexAction = 'user/default/index';
    /**
     * 
     * @param type $arr
     * @return type array
     */
    protected function mysort($arr)
    {
        $length = count($arr);

        while ($length != 0)
        {
            foreach ($arr as $k => $v)
            {
                if ($v == max($arr))
                {
                    $arr2[$k] = $v;
                    unset($arr[$k]);
                    $length--;
                }
            }
        }
        return $arr2;
    }

    /**
     * 
     * @param type $r
     * @param type $g
     * @param type $b
     * @param type $a
     * @return type string
     */
    protected function getColor($r, $g, $b, $a)
    {
        return 'rgba('.$r.','.$g.','.$b.','.$a.')';
    }

    public function run()
    {
        if (User::hasPermission('viewUsers'))
        {

            $visits = UserVisitLog::find()->all();

            if(empty($visits)) {
                return;
            }
            
            foreach ($visits as $visit)
            {
                if (!empty($visit->browser))
                {
                    $values[] = $visit->browser;
                }
            }
            $values = array_count_values($values);
            $total_count = count($values);
            
            $i = 0;

            foreach ($this->mysort($values) as $label => $count)
            {
                $labels[] = $label;
                $data[] = $count;
                $backgroundColor[] = $this->getColor(77, 117, 133, round(($total_count - $i) / $total_count, 2));
                $i++;
            }

            return $this->render('users-browser', [
                        'backgroundColor' => $backgroundColor,
                        'data' => $data,
                        'labels' => $labels,
            ]);
        }
    }
}