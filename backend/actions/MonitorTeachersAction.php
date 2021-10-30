<?php

namespace backend\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;

class MonitorTeachersAction extends Action
{
    public $widgets;
    public $layout;
    public $id;

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws NotFoundHttpException if the view file cannot be found
     */
    public function run()
    {
         $user = \common\models\teachers\Teachers::findOne($this->id)->getFullName();

        $this->controller->getView()->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
        $this->controller->getView()->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $this->id), 'url' => ['/teachers/default/view', 'id' => $this->id]];
        $this->controller->getView()->params['breadcrumbs'][] = 'Монитор: ' . $user;

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
     * @return string
     * @throws NotFoundHttpException
     */
    protected function render()
    {


        $content = '<div class="monitor_teachers">';
        $content .= '<div class="panel">';
        $content .= '<div class="panel-body">';

        foreach ($this->widgets as $row) {

            $content .= '<div class="row">';

            foreach ($row as $col) {

                if (!isset($col['class'])) {
                    throw new NotFoundHttpException(Yii::t('art', 'Invalid settings for dashboard widgets.'));
                }

                $content .= '<div class=' . $col['class'] . '>';

                foreach ($col['content'] as $widget) {
                    if (is_string($widget)) {
                        $content .= $widget::widget(['id' => $this->id]);
                    } else {
                        throw new NotFoundHttpException(Yii::t('art', 'Invalid settings for dashboard widgets.'));
                    }
                }
                $content .= '</div>';
            }
            $content .= '</div>';
        }

        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        return $this->controller->renderContent($content);
    }

}
