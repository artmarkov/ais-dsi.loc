<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 05.10.2018
 * Time: 12:14
 */

namespace backend\controllers;


class DefaultController  extends \artsoft\controllers\admin\BaseController {

    public $layout = '@artsoft/views/layouts/main.php';

    public function debug($arr){
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }
}