<?php

namespace backend\controllers\venue;

use common\models\venue\VenueSity;
use common\models\venue\VenueDistrict;

/**
 * DefaultController implements the CRUD actions for common\models\venue\VenuePlace model.
 */
class DefaultController extends MainController {

    public $modelClass = 'common\models\venue\VenuePlace';
    public $modelSearchClass = 'common\models\venue\search\VenuePlaceSearch';

    /**
     *  формируем список городов для widget DepDrop::classname()
     * @return false|string
     */
    public function actionSity() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = VenueSity::getSityByCountryId($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     *  формируем список районов для widget DepDrop::classname()
     * @return false|string
     */
    public function actionDistrict() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $subcat_id = $parents[0];
                $out = VenueDistrict::getDistrictBySityId($subcat_id);


                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }
}
