<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\question\Question;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class QuestionHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'question_hist';
    }

    public static function getModelName()
    {
        return Question::class;
    }

    protected function getFields()
    {
        return [
            'author_id',
            'name',
            'category_id',
            'users_cat',
            'vid_id',
            'division_list',
            'moderator_list',
            'description',
            'timestamp_in',
            'timestamp_out',
            'status',
            'email_flag',
            'email_author_flag',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'category_id':
                return isset($model->category_id) ? $model::getCategoryValue($value) : $value;
            case 'vid_id':
                return isset($model->vid_id) ? $model::getVidValue($value) : $value;
            case 'author_id':
                return isset($model->author->userCommon) ? $model->author->userCommon->fullName : $value;
            case 'moderator_list':
                if (isset($model->moderator_list)) {
                    $v = [];
                    foreach (Json::decode($model->moderator_list) as $id) {
                        $v[] = $id != null ? (UserCommon::findOne(['user_id' => $id]) ? UserCommon::findOne(['user_id' => $id])->getFullName() : $id) : null;
                    }
                    return implode(', ', $v);
                }
                case 'division_list':
                if (isset($model->division_list)) {
                    $v = [];
                    foreach (Json::decode($model->division_list) as $id) {
                        $v[] = $id != null ? RefBook::find('division_name')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (QuestionAttributeHistory::getLinkedIdList('question_id', $this->objId) as $questId) {
            $vf = new QuestionAttributeHistory($questId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}