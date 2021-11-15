<?php

namespace backend\controllers\info;

use backend\controllers\DefaultController;

class CatalogController extends DefaultController
{

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Каталог файлов';
        return $this->render('index');
    }
}
