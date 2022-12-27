<?php

namespace backend\controllers\education;

/**
 * EducationCatController implements the CRUD actions for common\models\education\EducationUnion model.
 */
class EducationUnionController extends MainController
{
    public $modelClass       = 'common\models\education\EducationUnion';
    public $modelSearchClass = 'common\models\education\search\EducationUnionSearch';

}