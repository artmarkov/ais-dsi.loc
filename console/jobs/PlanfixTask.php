<?php

namespace console\jobs;

use artsoft\helpers\Html;
use common\models\planfix\Planfix;
use common\models\user\UsersView;
use Yii;

/**
 * Class PlanfixTask.
 */
class PlanfixTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        if (Yii::$app->settings->get('mailing.planfix_mailing')) {
            $models = Planfix::find()
                ->where(['status' => Planfix::STATUS_ACTIVE])
                ->andWhere(['NOT IN', 'category_id', [1000, 1001, 1002]])
                ->all();
            if ($models) {
                foreach ($models as $item => $model) {
                    $author = UsersView::find()->where(['id' => $model->planfix_author])->one();
                    $planfix_timestamp = Yii::$app->formatter->asTimestamp($model->planfix_date);
                    $days = intdiv($planfix_timestamp - time(), 86400);

                    if (in_array($days, [40, 30, 10, 7, 5, 4, 3, 2, 1, 0, -1, -3, -5, -10])) {
                        $message = '<p>Вам назначена работа: ' . $model->name . ' . </p>';
                        $message .= '<p>Описание: ' . $model->description . '</p>';
                        $message .= '<p>Срок сдачи работы: ' . $model->planfix_date . '</p>';
                        $message .= '<hr>';

                        if ($days > 0) {
                            $message .= '<p>До сдачи работы осталось ' . $days . ' дня(дней).</p>';
                        } elseif ($days < 0) {
                            $message .= '<p>Вы просрочили выполнение работы на : ' . $days . ' дня(дней).</p>';
                        } elseif ($days == 0) {
                            $message .= '<p>Работу нужно сдать сегодня.</p>';
                        }
                        $message .= '<hr>';
                        $message .= '<p><b>С уважением, </b>' . Html::encode($author->user_name) . '</p>';

                        $model->sendPlanfixMessage($message);
                    }
                }
            }

        }
    }
}
