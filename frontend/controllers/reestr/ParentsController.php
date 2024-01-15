<?php

namespace frontend\controllers\reestr;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\info\Document;
use common\models\info\search\DocumentSearch;
use common\models\service\UsersCard;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use Yii;


/**
 * DefaultController implements the CRUD actions for common\models\parents\Parents model.
 */
class ParentsController extends MainController
{
    public $modelClass = 'common\models\parents\Parents';
    public $modelSearchClass = 'common\models\parents\search\ParentsSearch';
    public $modelHistoryClass = 'common\models\history\ParentsHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/parents/default';
        parent::init();
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id, $readonly = true)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/parents', 'Parents'), 'url' => ['/reestr/parents/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_PARENTS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->studentDependence;

        return $this->render('_form', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка родителя', 'url' => ['/reestr/parents/view', 'id' => $id]],
        ];
    }
}