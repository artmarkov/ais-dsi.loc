<?php

namespace artsoft\web;

use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;

class DashboardAction extends Action
{
    public $widgets;
    public $layout;

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws NotFoundHttpException if the view file cannot be found
     */
    public function run()
    {
        $this->controller->getView()->title = Yii::t('art', 'Dashboard');

        $controllerLayout = null;
        if ($this->layout !== null) {
            $controllerLayout = $this->controller->layout;
            $this->controller->layout = $this->layout;
        }

        try {
            $output = $this->render();

            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }
        } catch (InvalidParamException $e) {

            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }

            if (YII_DEBUG) {
                throw new NotFoundHttpException($e->getMessage());
            } else {
                throw new NotFoundHttpException(
                    Yii::t('yii', 'The requested view was not found.')
                );
            }
        }

        return $output;
    }

    /**
     * Renders a view
     *
     * @return string result of the rendering
     */
    protected function render()
    {

        $content = '<div class="dashboard">';
        
        foreach ($this->widgets as $row) {

            $content .= '<div class="row">';

            foreach ($row as $col) {

                if (!isset($col['class']))
                {
                    throw new NotFoundHttpException(Yii::t('art', 'Invalid settings for dashboard widgets.'));
                }

                $content .= '<div class=' . $col['class'] . '>';
                
                foreach ($col['content'] as $widget) {
                    if (is_string($widget))
                    {
                        $content .= $widget::widget();
                    }
                    else  {
                        throw new NotFoundHttpException(Yii::t('art', 'Invalid settings for dashboard widgets.'));
                    }
                }
                $content .= '</div>';
            }
            $content .= '</div>';
        }
        $content .= '</div>';


        return $this->controller->renderContent($content);
    }

}
