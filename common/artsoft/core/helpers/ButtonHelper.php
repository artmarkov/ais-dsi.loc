<?php

namespace artsoft\helpers;

use Yii;

/**
 * Submit Button in form
 *
 */
class ButtonHelper
{
    const buttonClass = 'btn-md';

    /**
     * @param $model
     * @param string $indexAction
     * @param string $deleteAction
     * @param string $buttonClass
     * @return string
     */
    public static function submitButtons($model, $indexAction = null, $deleteAction = null, $buttonClass = self::buttonClass)
    {
        $result = self::exitButton($indexAction, $buttonClass);
        $result .= self::saveButton('submitAction', 'saveexit', 'Save & Exit', $buttonClass);
        $result .= self::saveButton('submitAction', 'save', 'Save', $buttonClass);
        $result .= $model->isNewRecord ? null /*self::saveButton('submitAction', 'savenext', 'Save & Add', $buttonClass)*/ : self::deleteButton($deleteAction, $buttonClass);
//         print_r(self::getResolve());
        return $result;
    }

    /**
     * @param $class
     * @param null $deleteAction
     * @param string $buttonClass
     * @return string
     */
    public static function modalButtons($class_cancel = 'cancel-event', $class_delete = 'delete-event', $buttonClass = self::buttonClass)
    {
        $result = self::closeButton($class_cancel, $buttonClass);
        $result .= self::saveButton('submitAction', 'save', 'Save', $buttonClass);
        $result .= self::deleteButtonModal($class_delete, $buttonClass);

        return $result;
    }

    /**
     * @param $model
     * @param string $indexAction
     * @param string $deleteAction
     * @param string $editAction
     * @param string $buttonClass
     * @return string
     */
    public static function viewButtons($model, $indexAction = null, $editAction = null, $deleteAction = null, $buttonClass = self::buttonClass)
    {
        $result = self::exitButton($indexAction, $buttonClass);
        $result .= self::updateButton($model, $editAction, $buttonClass);
        $result .= !$model->isNewRecord ? self::deleteButton($deleteAction, $buttonClass) : null;
        return $result;
    }

    /**
     * @param $createAction
     * @return string
     */
    public static function createButton($createAction = null, $options = [])
    {
        $createAction = $createAction == null ? self::getCreateAction() : $createAction;
        return Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art', 'Add New'), $createAction, array_merge(['class' => 'btn btn-sm btn-success'], $options));
    }

    /**
     * @param null $editAction
     * @param string $buttonClass
     * @return string
     */
    public static function updateButton($model, $editAction = null, $buttonClass = self::buttonClass)
    {
        $editAction = $editAction == null ? self::getEditAction($model) : $editAction;

        return Html::a(
            '<i class="fa fa-pencil" aria-hidden="true"></i> ' . Yii::t('art', 'Edit'),
            $editAction,
            [
                'class' => 'btn btn-primary ' . $buttonClass,
            ]
        );
    }

    /**
     * @param null $indexAction
     * @param string $buttonClass
     * @return string
     */
    public static function exitButton($indexAction = null, $buttonClass = self::buttonClass)
    {
        $indexAction = $indexAction == null ? self::getIndexAction() : $indexAction;

        return Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'),
            $indexAction,
            [
                'class' => 'btn btn-default ' . $buttonClass
            ]);
    }

    /**
     * @param null $class
     * @param string $buttonClass
     * @return string
     */
    public static function closeButton($class = null, $buttonClass = self::buttonClass)
    {

        return Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Cancel'),
            ['#'],
            [
                'class' => 'btn btn-default ' . $buttonClass . ' ' . $class
            ]);
    }

    /**
     * @param $model
     * @param null $deleteAction
     * @param string $buttonClass
     * @return string
     */
    public static function deleteButton($deleteAction = null, $buttonClass = self::buttonClass)
    {
        $deleteAction = $deleteAction == null ? self::getDeleteAction() : $deleteAction;

        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
            $deleteAction,
            [
                'class' => 'btn btn-danger ' . $buttonClass,
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
    }

    /**
     * @param null $class
     * @param string $buttonClass
     * @return string
     */
    public static function deleteButtonModal($class = null, $buttonClass = self::buttonClass)
    {

        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
            ['#'],
            [
                'class' => 'btn btn-danger ' . $buttonClass . ' ' . $class,
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $message
     * @param string $buttonClass
     * @return string
     */
    public static function saveButton($name = 'submitAction', $value = 'save', $message = 'Save', $buttonClass = self::buttonClass)
    {
        return Html::submitButton(
            '<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', $message),
            [
                'class' => ($value == 'savenext' ? 'btn btn-success ' : 'btn btn-primary ') . $buttonClass,
                'name' => $name,
                'value' => $value,
            ]
        );
    }

    /**
     * @param $model
     * @param null $historyAction
     * @param string $buttonClass
     * @return string
     */
    public static function historyButton($historyAction = null, $buttonClass = self::buttonClass)
    {
        $historyAction = $historyAction == null ? self::getHistoryAction() : $historyAction;
        return Html::a('<i class="fa fa-history" aria-hidden="true"></i> ' . Yii::t('art', 'History'),
            $historyAction,
            [
                'class' => 'btn btn-default ' . $buttonClass,
            ]
        );
    }

    /**
     * @param $action
     * @return string
     */
    protected static function getAction($action)
    {
        return '/' . Yii::$app->controller->id . '/' . $action;
    }

    /**
     * @return array
     */
    public static function getResolve()
    {
        return Yii::$app->request->resolve();
    }

    /**
     * @return array
     */
    public static function getIndexAction()
    {
        $arr = [];
        $url = self::getResolve();
        if (!empty(preg_filter("/\/view|\/update|\/create|\/delete/", "", $url[0]))) {
            $arr[] = preg_filter("/\/view|\/update|\/create|\/delete/", "", $url[0]) . '/index';
        } else {
            $arr[] = $url[0];
            isset($url[1]['id']) ? $arr['id'] = $url[1]['id'] : null;
        }
        return $arr;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getEditAction($model)
    {
        $url = self::getResolve();
        isset($url[1]['id']) ? $arr['id'] = $url[1]['id'] : null;
        if (isset($url[1]['mode'])) {
            $arr[] = $url[0];
            $arr['objectId'] = isset($url[1]['objectId']) ? $url[1]['objectId'] : $model->id;
            $arr['mode'] = 'update';
        } else {
            $url[0] = str_replace('create', 'update', $url[0]);
            $arr[] = str_replace('view', 'update', $url[0]);
            isset($model->id) ? $arr['id'] = $model->id : false;
        }

        return $arr;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getDeleteAction()
    {
        $url = self::getResolve();
        isset($url[1]['id']) ? $arr['id'] = $url[1]['id'] : null;
        if (isset($url[1]['mode'])) {
            $arr[] = $url[0];
            $arr['objectId'] = isset($url[1]['objectId']) ? $url[1]['objectId'] : null;
            $arr['mode'] = 'delete';
        } else {
            $arr[] = str_replace('update', 'delete', $url[0]);
        }

        return $arr;
    }

    /**
     * @return array
     */
    public static function getCreateAction()
    {
        $arr = [];
        $url = self::getResolve();
        if (isset($url[1]['id'])) {
            $arr[] = $url[0];
            $arr['id'] = $url[1]['id'];
            $arr['mode'] = 'create';
        } else {
            $arr[] = str_replace('index', 'create', $url[0]);
        }
        return $arr;
    }

    /**
     * @return array
     */
    public static function getHistoryAction()
    {
        $arr = [];
        $url = self::getResolve();
        isset($url[1]['id']) ? $arr['id'] = $url[1]['id'] : null;
        if (isset($url[1]['mode'])) {
            $arr[] = $url[0];
            $arr['objectId'] = isset($url[1]['objectId']) ? $url[1]['objectId'] : null;
            $arr['mode'] = 'history';
        } else {
            if (!empty(preg_filter("/\/view|\/update|\/create/", "", $url[0]))) {
                $arr[] = preg_filter("/\/view|\/update|\/create/", "", $url[0]) . '/history';
            }
        }

        return $arr;
    }
}