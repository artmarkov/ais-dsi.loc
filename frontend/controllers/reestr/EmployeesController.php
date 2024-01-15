<?php

namespace frontend\controllers\reestr;

use common\models\service\UsersCard;
use common\models\user\UserCommon;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\employees\Employees model.
 */
class EmployeesController extends MainController
{
    public $modelClass = 'common\models\employees\Employees';
    public $modelSearchClass = 'common\models\employees\search\EmployeesSearch';
    public $modelHistoryClass = 'common\models\history\EmployeesHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/employees/default';
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
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/employees', 'Employees'), 'url' => ['/reestr/employees/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_EMPLOYEES]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
//        $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon, $userCard)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        return $this->render('_form', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
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
            ['label' => 'Карточка сотрудника', 'url' => ['/reestr/employees/view', 'id' => $id]],
        ];
    }
}