<?php

namespace backend\controllers\subject;

use common\models\subject\Subject;

/**
 * DefaultController implements the CRUD actions for common\models\subject\Subject model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\subject\Subject';
    public $modelSearchClass = 'common\models\subject\search\SubjectSearch';

    /**
     *  формируем список дисциплин для widget DepDrop::classname()
     * @return false|string
     */
    public function actionSubject()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = Subject::getSubjectById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }
}