<?php

namespace artsoft\controllers\admin;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\ButtonHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use artsoft\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

abstract class BaseController extends \artsoft\controllers\BaseController
{
    /**
     * @var ActiveRecord
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    public $modelSearchClass;

    /**
     * @var ActiveRecord
     */
    public $modelHistoryClass;


    /**
     * Actions that will be disabled
     *
     * List of available actions:
     *
     * ['index', 'view', 'create', 'update', 'delete', 'toggle-attribute',
     * 'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size']
     *
     * @var array
     */
    public $disabledActions = [];

    /**
     * Opposite to $disabledActions. Every action from AdminDefaultController except those will be disabled
     *
     * But if action listed both in $disabledActions and $enableOnlyActions
     * then it will be disabled
     *
     * @var array
     */
    public $enableOnlyActions = [];

    /**
     * List of actions in this controller. Needed fo $enableOnlyActions
     *
     * @var array
     */
    protected $_implementedActions = ['index', 'view', 'create', 'update', 'delete',
        'toggle-attribute', 'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size'];

    /**
     * Layout file for admin panel
     *
     * @var string
     */
    public $layout = '@artsoft/views/layouts/admin/main.php';

    /**
     * Index page view
     *
     * @var string
     */
    public $indexView = 'index';

    /**
     * View page view
     *
     * @var string
     */
    public $viewView = 'view';

    /**
     * Create page view
     *
     * @var string
     */
    public $createView = 'create';

    /**
     * Update page view
     *
     * @var string
     */

    public $updateView = 'update';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Получаем параметры, сохраняя поисковую модель
     * @return array|mixed
     */
    public function getParams()
    {
        $searchModel = $this->modelSearchClass;
        if ($searchModel) {
            $params = Yii::$app->request->getQueryParams();
            $searchName = StringHelper::basename($searchModel::className());
            $session = Yii::$app->session;
//            print_r(Yii::$app->request->referrer);
//            print_r($params);
            if (count($params) == 0 || (count($params) == 1 && isset($params['id']))) {
                if (preg_match('/update|create|view/', Yii::$app->request->referrer) || empty(Yii::$app->request->referrer)) { // если нет параметров и зашли с update или create, то берем параметры из сессии
                    if ($params = $session->get($searchName . '_params')) {
                        $_GET = $params; // и прописываем в GET для сохранения pagination
                    }
                } else {
                    $session->remove($searchName . '_params'); // если зашли с index, то очищаем пораметры сессии
                }
            } else {
                $session->set($searchName . '_params', $params); // если параметры заданы, записываем их в сессию
            }
            return $params;
        }
        return [];
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $modelClass = $this->modelClass;
        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;
        $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
            && !User::hasPermission($modelClass::getFullAccessPermission()));

        if ($searchModel) {
            $searchName = StringHelper::basename($searchModel::className());
            $params = $this->getParams(); //TODO 
            // $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $dataProvider = $searchModel->search($params);
        } else {
            $restrictParams = ($restrictAccess) ? [$modelClass::getOwnerField() => Yii::$app->user->identity->id] : [];
            $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()->where($restrictParams)]);
        }

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    /**
     * Displays a single model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->renderIsAjax($this->viewView, [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, compact('model'));
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }

    /**
     * History action
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new $this->modelHistoryClass($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }

    /**
     * @param string $attribute
     * @param int $id
     */
    public function actionToggleAttribute($attribute, $id)
    {
        //TODO: Restrict owner access
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
        $model->save(false);
    }

    /**
     * Activate all selected grid items
     */
    public function actionBulkActivate()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $where = ['id' => Yii::$app->request->post('selection', [])];

            if ($restrictAccess) {
                $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $modelClass::updateAll(['status' => 1], $where);
        }
    }

    /**
     * Deactivate all selected grid items
     */
    public function actionBulkDeactivate()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $where = ['id' => Yii::$app->request->post('selection', [])];

            if ($restrictAccess) {
                $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $modelClass::updateAll(['status' => 0], $where);
        }
    }

    /**
     * Deactivate all selected grid items
     */
    public function actionBulkDelete()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));

            foreach (Yii::$app->request->post('selection', []) as $id) {
                $where = ['id' => $id];

                if ($restrictAccess) {
                    $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
                }

                $model = $modelClass::findOne($where);

                if ($model) $model->delete();
            }
        }
    }

    /**
     * Sorting items in grid
     */
    public function actionGridSort()
    {
        if (Yii::$app->request->post('sorter')) {
            $sortArray = Yii::$app->request->post('sorter', []);

            $modelClass = $this->modelClass;

            $models = $modelClass::findAll(array_keys($sortArray));

            foreach ($models as $model) {
                $model->sorter = $sortArray[$model->id];
                $model->save(false);
            }
        }
    }

    /**
     * Set page size for grid
     */
    public function actionGridPageSize()
    {
        if (Yii::$app->request->post('grid-page-size')) {
            $cookie = new Cookie([
                'name' => '_grid_page_size',
                'value' => Yii::$app->request->post('grid-page-size'),
                'expire' => time() + 86400 * 365, // 1 year
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $id
     *
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        $model = new $modelClass;

        if (method_exists($model, 'isMultilingual') && $model->isMultilingual()) {
            $condition = [];
            $primaryKey = $modelClass::primaryKey();
            $query = $modelClass::find();

            if (isset($primaryKey[0])) {
                $condition = [$primaryKey[0] => $id];
            } else {
                throw new InvalidConfigException('"' . Pos . '" must have a primary key.');
            }

            $model = $query->andWhere($condition)->multilingual()->one();
        } else {
            $model = $modelClass::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    /**
     * Define redirect page after submit save action
     * @param null $model
     * @param null $submitAction
     * @return \yii\web\Response
     */
    protected function getSubmitAction($model = null, $submitAction = null)
    {
        $submitAction = $submitAction == null ? Yii::$app->request->post('submitAction', 'save') : $submitAction;
        switch ($submitAction) {
            case 'savenext':
                return $this->redirect($this->getRedirectPage('create', $model));
                break;
            case 'saveexit':
                return $this->redirect($this->getRedirectPage('index'));
                break;
            default:
                return $this->redirect($this->getRedirectPage('update', $model));

        }

    }

    /**
     * Define redirect page after update, create, delete, etc
     * @param $action
     * @param null $model
     * @return array
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'index':
            case 'delete':
                return ButtonHelper::getIndexAction();
                break;
            case 'update':
                return ButtonHelper::getEditAction($model);
                break;
            case 'create':
                return ButtonHelper::getCreateAction($model);
                break;
            default:
                return ButtonHelper::getIndexAction();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            if ($this->enableOnlyActions !== [] AND in_array($action->id, $this->_implementedActions) AND
                !in_array($action->id, $this->enableOnlyActions)
            ) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            if (in_array($action->id, $this->disabledActions)) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return true;
        }

        return false;
    }
}