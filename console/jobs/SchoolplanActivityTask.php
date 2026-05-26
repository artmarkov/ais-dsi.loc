<?php

namespace console\jobs;

use artsoft\helpers\Html;
use common\models\schoolplan\Schoolplan;
use common\models\schoolplan\SchoolplanActivity;
use common\models\schoolplan\SchoolplanView;
use common\models\user\UsersView;
use Yii;

/**
 * Class SchoolplanActivityTask.
 */
class SchoolplanActivityTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        if (Yii::$app->settings->get('mailing.schoolplan_mailing')) {
            $models = SchoolplanActivity::find()
                ->where(['activity_status' => 1])
                ->andWhere(['>=', 'datetime_in', time() - (86400 * 3)])
                ->all();
            if ($models) {
                foreach ($models as $item => $model) {
                    $author = UsersView::find()->where(['id' => $model->author_id])->one();
                    $executor = UsersView::find()->where(['id' => $model->executor_id])->one();
                    $schoolplan = SchoolplanView::find()->where(['id' => $model->schoolplan_id])->one();
                    $timestamp = Yii::$app->formatter->asTimestamp($model->datetime_in);
                    $days = intdiv($timestamp - time(), 86400);

                    if (in_array($days, [30, 10, 5, 3, 1, 0, -1, -3])) {
                        $message = '<p><b>Здравствуйте, ' . Html::encode($executor->first_name . ' ' . $executor->middle_name) . '</b></p>';
                        $message .= '<p>Вам назначена работа: ' . $model->name . ' . </p>';
                        $message .= '<p>Мероприятие: ' . $schoolplan->title . ' (' . Yii::$app->formatter->asDatetime($schoolplan->datetime_in). ')</p>';
                        if ($schoolplan->auditory_places) {
                            $message .= '<p>Место проведения мероприятия: ' . $schoolplan->auditory_places . '</p>';
                        }
                        if ($schoolplan->description) {
                            $message .= '<p>Описание мероприятия: ' . $schoolplan->description . '</p>';
                        }
                        $message .= '<hr>';
                        $message .= '<p>Место работы: ' . $model->places . '</p>';
                        if ($model->author_comment) {
                        $message .= '<p>Описание работы: ' . $model->author_comment . '</p>';
                        }
                        $message .= '<p>Дата и время начала работы: ' . $model->datetime_in . '</p>';
                        $message .= '<hr>';

                        if ($days > 0) {
                            $message .= '<p>До выполнения работы осталось ' . $days . ' дня(дней).</p>';
                        } elseif ($days < 0) {
                            $message .= '<p>Вы просрочили выполнение работы на : ' . abs($days) . ' дня(дней).</p>';
                        } elseif ($days == 0) {
                            $message .= '<p>Работу нужно выполнить сегодня.</p>';
                        }
                        $message .= '<hr>';
                        $message .= '<p><b>С уважением, </b>' . Html::encode($author->first_name . ' ' . $author->middle_name) . '</p>';

                        $model->sendActivityMessage($message);
                    }
                }
            }
        }
    }
}
