<?php

namespace artsoft;

use artsoft\models\User;
use yii\debug\Module;
use Yii;

class DebugModule extends Module
{

    private $_basePath;

    protected function checkAccess($action = null)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (Yii::$app->user->identity && User::hasPermission('viewDebug')) {
            return true;
        }
        return true;
    }

    /**
     * Returns the root directory of the module.
     * It defaults to the directory containing the module class file.
     * @return string the root directory of the module.
     * @throws \ReflectionException
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass(new \yii\debug\Module('debug'));
            $this->_basePath = dirname($class->getFileName());
        }

        return $this->_basePath;
    }

}
