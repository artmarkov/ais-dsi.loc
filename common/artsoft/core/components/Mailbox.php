<?php

namespace artsoft\components;

use artsoft\models\User;
use yii\base\Component;
use yii\web\NotFoundHttpException;

/**
 * Class Mailbox
 * Usage examples:
 * ~~~
 * Yii::$app->mailbox->send([1000,1001],'Title','Content');
 * ~~~
 * @package artsoft\components
 */
class Mailbox extends Component
{
    public $modelClass = 'artsoft\mailbox\models\Mailbox';

    /**
     * @param $receiversIds
     * @param null $title
     * @param null $content
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function send($receiversIds, $title = NULL, $content = NULL)
    {
        if (!$receiversIds) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр receiversIds.");
        }
        if (!is_array($receiversIds)) {
            $receiversIds = [$receiversIds];
        }
        $model = new $this->modelClass;
        $model->scenario = $this->modelClass::SCENARIO_COMPOSE;
        $model->status_post = $this->modelClass::STATUS_POST_SENT;
        $model->receivers_ids = $this->getReceivers($receiversIds);
        $model->title = strip_tags($title);
        $model->content = $content;

        return $model->save();
    }

    /**
     * @param $receiversIds
     * @return mixed
     */
    protected function getReceivers($receiversIds)
    {
        foreach ($receiversIds as $item => $id) {
            $model = User::find()
                ->where(['=', 'id', $id])
                ->andWhere(['=', 'status', User::STATUS_ACTIVE])
                ->exists();
            if (!$model) {
                unset($receiversIds[$item]);
            }
        }
        return $receiversIds;
    }
}