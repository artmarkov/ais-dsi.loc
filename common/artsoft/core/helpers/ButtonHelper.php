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
        $result .= $model->isNewRecord ? self::saveButton('submitAction', 'savenext', 'Save & Add', $buttonClass) : self::deleteButton($model, $deleteAction, $buttonClass);

        return $result;
    }

    /**
     * @param $model
     * @param $class
     * @param null $deleteAction
     * @param string $buttonClass
     * @return string
     */
    public static function modalButtons($model, $class = 'cancel-event', $deleteAction = null, $buttonClass = self::buttonClass)
    {
        $result = self::closeButton($class, $buttonClass);
        $result .= self::saveButton('submitAction', 'save', 'Save', $buttonClass);
        $result .= self::deleteButton($model, $deleteAction, $buttonClass);

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
        $result .= !$model->isNewRecord ? self::deleteButton($model, $deleteAction, $buttonClass) : null;

        return $result;
    }

    /**
     * @param $createAction
     * @return string
     */
    public static function createButton($createAction = null)
    {
        $createAction = $createAction == null ? self::getAction('create') : $createAction;

        return Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art', 'Add New'), [$createAction], ['class' => 'btn btn-sm btn-success']);
    }

    /**
     * @param $model
     * @param null $editAction
     * @param string $buttonClass
     * @return string
     */
    public static function updateButton($model, $editAction = null, $buttonClass = self::buttonClass)
    {
        $editAction = $editAction == null ? [self::getAction('update'), 'id' => $model->id] : $editAction;

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
        $indexAction = $indexAction == null ? self::getAction('index') : $indexAction;

        return Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'),
            [$indexAction],
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
    public static function deleteButton($model, $deleteAction = null, $buttonClass = self::buttonClass)
    {
        $deleteAction = $deleteAction == null ? [self::getAction('delete'), 'id' => $model->id] : $deleteAction;

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
     * @param string $name
     * @param string $value save & saveexit
     * @param string $buttonClass
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
     * @param $action
     * @return string
     */
    protected static function getAction($action)
    {
        return '/' . Yii::$app->controller->id . '/' . $action;
    }
}