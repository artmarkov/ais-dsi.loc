<?php

namespace frontend\widgets\board;

use artsoft\models\User;
use common\models\info\Board;
use yii\base\Widget;

class BoardWidget extends Widget
{
    public $modelClass       = 'common\models\info\Board';
    public $modelSearchClass = 'common\models\info\search\BoardSearch';

    public function run()
    {
        $categoryIds = [];
        if(User::hasRole(['teacher','department'])) {
            $categoryIds = [Board::CAT_ALL, Board::CAT_TEACHERS];
        } elseif(User::hasRole(['student'])) {
            $categoryIds = [Board::CAT_ALL, Board::CAT_STUDENTS];
        } elseif(User::hasRole(['parents'])) {
            $categoryIds = [Board::CAT_ALL, Board::CAT_PARENTS];
        } elseif(User::hasRole(['employees'])) {
            $categoryIds = [Board::CAT_ALL, Board::CAT_EMPLOYEES];
        }
        $userId = \Yii::$app->user->identity->getId();

        $models = Board::find()
            ->where(['>', 'delete_date', time()])
            ->andWhere(['=', 'status', Board::STATUS_ACTIVE])
            ->andWhere(new \yii\db\Expression('CASE WHEN recipients_list IS NOT NULL THEN category_id = :category_id  AND :user_id = any(string_to_array(recipients_list, \',\')::int[]) ELSE category_id = any(string_to_array(:category_ids, \',\')::int[]) END', [':category_id' => Board::CAT_SELECT, ':user_id' => $userId, ':category_ids' => implode(',', $categoryIds)]))
            ->orderBy('board_date DESC')->all();
        return $this->render('board', ['models' => $models]);
    }

}