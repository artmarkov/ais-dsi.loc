<?php
namespace frontend\controllers\education;

use common\models\education\EducationProgramm;

class DefaultController extends \frontend\controllers\DefaultController
{
    public $freeAccessActions = ['programm'];

    public function actionProgramm()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = EducationProgramm::getProgrammListById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

}