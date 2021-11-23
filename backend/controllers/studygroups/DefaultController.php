<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 05.10.2018
 * Time: 12:14
 */

namespace backend\controllers\studygroups;

use common\models\studygroups\SubjectSect;

class DefaultController  extends MainController
{
    public $modelClass = 'common\models\studygroups\SubjectSect';
    public $modelSearchClass = 'common\models\studygroups\search\SubjectSectSearch';

    public function actionSubject()
    {

        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $cat_id = $parents[1];
                $out = $this->modelClass::getSubjectForUnionAndCatToId($union_id, $cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }
    public function actionSubjectCat()
    {
        
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $out = $this->modelClass::getSubjectCategoryForUnionToId($union_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }
}