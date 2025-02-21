<?php

namespace common\models\concourse;

use artsoft\models\User;
use common\widgets\editable\Editable;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ConcourseAnswers
{
    public $id;
    public $model;
    public $modelsItems;
    public $objectId;
    public $userId;
    public $modelsCriteria;
    public $modelsCriteriaCount;
    public $models;

    const mark_list = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10]; // веса оценок

    public function __construct($config = [])
    {
        $this->id = $config['id'];
        $this->objectId = $config['objectId'] ?? false;
        $this->userId = $config['userId'] ?? false;
        $this->model = $this->getModelConcourse(); // модель конкурса
        $this->modelsCriteria = $this->getModelsCriteria(); // все критерии конкурса
        $this->modelsCriteriaCount = $this->modelsCriteria->count(); // число критериев
        $this->modelsItems = $this->getModelsConcourseItems(); // все модели работ
    }

    public function getUserFio()
    {
        if (!$this->userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        return (new Query())->from('users_view')
            ->select('user_name')
            ->where(['id' => $this->userId])
            ->scalar();
    }
    /**
     * Проверка, есть ли пользователь в списке участников конкурса
     * @return bool
     * @throws NotFoundHttpException
     */
    public function isUsers()
    {
        if (!$this->userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        $users_list = $this->getUsers();
        return in_array($this->userId, $users_list);
    }

    /**
     * Проверка, может ли пользователь оценивать текущую работу
     * @return bool
     * @throws NotFoundHttpException
     */
    public function isUsersItem($itemId = false)
    {
        if (!$this->userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        if ($itemId) {
            $this->objectId = $itemId;
        }
        if (!$this->objectId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр objectId.");
        }
        $users_list = $this->getUsersForItem($this->objectId);
        return in_array($this->userId, $users_list);
    }

    /**
     * Показатель заполненности работы в % 100% - полностью заполнена участниками
     * @param $item_id
     * @return float|int
     */
    public function getConcourseItemFullness($item_id)
    {
        $users_list = $this->getUsersForItem($item_id);
        return $users_list ? ConcourseValue::find()->where(['concourse_item_id' => $item_id])->andWhere(['users_id' => $users_list])->count() / $this->modelsCriteriaCount / count($users_list) * 100 : 0;
    }

    /**
     * Показатель заполнения работы пользователем (0/1)
     * @param $item_id
     * @return bool
     * @throws NotFoundHttpException
     */
    public function getConcourseItemFullnessForUser($item_id)
    {
        if (!$this->userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        return ConcourseValue::find()->where(['concourse_item_id' => $item_id])->andWhere(['users_id' => $this->userId])->count() === $this->modelsCriteriaCount;
    }

    /**
     * Показатель заполненности участником всех работ в % 100% - полностью заполнены участником все работы
     * @param $user_id
     * @return float|int
     */
    /*public function getConcourseUserFullness($user_id)
    {
        return ConcourseValue::find()->where(['concourse_item_id' => $item_id])->andWhere(['users_id' => $user_id])->count() / $this->modelsCriteriaCount / count($users_list) * 100;
    }*/

    /**
     * Средняя оценка работы
     * @param $item_id
     * @return float|int
     */
    public function getMiddleMark($item_id)
    {
        $users_list = $this->getUsersForItem($item_id);
        $countUsers = ConcourseValue::find()->select('users_id')->distinct()->where(['concourse_item_id' => $item_id])->andWhere(['users_id' => $users_list])->count();
        return $countUsers != 0 ? ConcourseValue::find()->select(new \yii\db\Expression('SUM(concourse_mark)'))->where(['concourse_item_id' => $item_id])->andWhere(['users_id' => $users_list])->scalar() / $this->modelsCriteriaCount / $countUsers : 0;
    }

    /**
     * Модели для заполнения оценок участником
     * @param $userId
     * @return array
     * @throws NotFoundHttpException
     */
    public function getAnswersConcourseUsers()
    {
        $modelsItems = [];
        if (!$this->userId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр userId.");
        }
        if (!$this->objectId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр objectId.");
        }
        $models = $this->modelsCriteria->all();

        foreach ($models as $item => $model) {
            $m = ConcourseValue::find()->where(['users_id' => $this->userId])
                ->andWhere(['concourse_item_id' => $this->objectId])
                ->andWhere(['concourse_criteria_id' => $model->id])
                ->one();
            if (!$m) {
                $m = new ConcourseValue();
                $m->users_id = $this->userId;
                $m->concourse_item_id = $this->objectId;
                $m->concourse_criteria_id = $model->id;
               // $m->save(false);
            }
            $modelsItems[] = $m;
        }
//        echo '<pre>' . print_r($modelsItems, true) . '</pre>'; die();
        return $modelsItems;
    }

    public function getModelConcourse()
    {
        return Concourse::findOne($this->id);
    }

    public function getModelsConcourseItems()
    {
        return ConcourseItem::find()->where(['concourse_id' => $this->id])->all();
    }

    public function getModelConcourseItem($item_id)
    {
        return ConcourseItem::findOne($item_id);
    }

    public function getModelConcourseItemValues($item_id)
    {
        return ConcourseValue::find()->where(['concourse_item_id' => $item_id])->asArray()->all();
    }

    public function getMarkList()
    {
        return self::mark_list;
    }

    public function getModelsCriteria()
    {
        return ConcourseCriteria::find()->where(['concourse_id' => $this->id])->orderBy('sort_order');
    }

    /**
     * Все участники для оценки(могут участвовать как авторы работ так и отдельные участники, указанные в карточке конкурса)
     * @return array|string|null
     */
    public function getUsers()
    {
        if ($this->model->vid_id == Concourse::VID_USERS) {
            $users_list = $this->model->users_list;
        } else {
            $users_list = $this->getAllUsers();
        }
        return $users_list;
    }

    /**
     * Все участники для оценки работы
     * @param $item_id
     * @return array|string|null
     */
    public function getUsersForItem($item_id)
    {
        $users_list = $this->getUsers();
        if ($this->model->authors_ban_flag) {
            $modelItem = $this->getModelConcourseItem($item_id); // модель работы
//            echo '<pre>' . print_r($modelItem, true) . '</pre>'; die();
            $users_list = array_diff($users_list, $modelItem->authors_list); // авторы не участвуют в оценке
        }
        return $users_list;
    }

    /** Полный список авторов всех работ
     * @return array
     */
    public function getAllUsers()
    {
        $users_list = [];
            $users = ArrayHelper::getColumn($this->modelsItems, 'authors_list');
            foreach ($users as $item => $val) {
                $users_list = array_merge($users_list, $val);
            }

        return array_unique($users_list);
    }
    /**
     * Названия критерий оценки работ конкурса
     * @return array
     */
    public function attributesCriteria()
    {
        return ArrayHelper::map($this->modelsCriteria->asArray()->all(), 'id', 'name');
    }

    public function attributesCriteriaDev()
    {
        return ArrayHelper::map($this->modelsCriteria->asArray()->all(), 'id', 'name_dev');
    }

    public function getData()
    {
        if (!$this->objectId) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр objectId.");
        }

        $data = [];
        $root = $this->attributesCriteriaDev();

        $attributes = ['name' => 'Участник'];
        $attributes += $root;
        $attributes += ['summ' => 'Сумма'];
        $attributes += ['mid' => 'Среднее'];

        $models = $this->getModelConcourseItemValues($this->objectId); // все ответы работы;
        $res = [];
        $all_summ = 0;
        foreach ($models as $model) {
            $res[$model['users_id']][$model['concourse_criteria_id']] = self::getEditableForm($model['concourse_mark'], $model['users_id'], $this->objectId, $model['concourse_criteria_id']);
            $res[$model['users_id']]['summ'] = isset($res[$model['users_id']]['summ']) ? $res[$model['users_id']]['summ'] + $model['concourse_mark'] : $model['concourse_mark'];
        }
        $users_list = $this->getUsersForItem($this->objectId);
        $users = User::getUsersByIds($users_list);
        $countUsers = ConcourseValue::find()->select('users_id')->distinct()->where(['concourse_item_id' => $this->objectId])->andWhere(['users_id' => $users_list])->count();

        foreach ($users as $id => $name) {
            $data[$id] = $res[$id] ?? ['summ' => null];
            $data[$id]['modelId'] = $this->id;
            $data[$id]['objectId'] = $this->objectId;
            $data[$id]['id'] = $id;
            $data[$id]['name'] = $name;
            $data[$id]['mid'] = $data[$id]['summ'] / count($root);
            $all_summ += isset($res[$id]['summ']) ? $res[$id]['summ'] : null;
        }
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        usort($data, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        $all_mid = (count($root) != 0 && $countUsers != 0) ? $all_summ / count($root) / $countUsers : 0;
        return ['data' => $data, 'all_mid' => $all_mid, 'attributes' => $attributes, 'root' => $root];
    }

    public function getStatData()
    {
        $data = [];

        $attributes = ['name' => 'Участник'];
        $attributes += ['scale' => 'Шкала заполнения'];

        $users_list = $this->getUsers();
        $users = User::getUsersByIds($users_list);

        foreach ($users as $id => $name) {
            $data[$id]['name'] = $name;
            $models = (new Query())->from('concourse_item')
                ->select(new \yii\db\Expression('concourse_value.users_id users_id, concourse_item.id id, count(concourse_value.id) count'))
                ->innerJoin('concourse_value', 'concourse_item.id = concourse_value.concourse_item_id')
                ->where(['concourse_item.concourse_id' => $this->id])
                ->groupBy(['concourse_value.users_id', 'concourse_item.id'])
                ->all();
            $models = ArrayHelper::index($models, 'id',['users_id']);
            $modelsUsers = $models[$id] ?? [];
       // echo '<pre>' . print_r($models, true) . '</pre>'; die();
            $data[$id]['scale'] = $this->getCheckLabels($id, $modelsUsers);
        }
        usort($data, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return ['data' => $data, 'attributes' => $attributes];
    }

    protected function getCheckLabels($userId, $modelsUsers)
    {
        $models = ConcourseItem::find()->where(['concourse_id' => $this->id]);
        if ($this->model->authors_ban_flag) {
            $models = $models->andWhere(new \yii\db\Expression("{$userId} <> all (string_to_array(authors_list, ',')::int[])"));
        }
        $models = $models->orderBy(['id' => SORT_ASC])->asArray()->all();
//        echo '<pre>' . print_r($models, true) . '</pre>'; die();
        $check = [];
        foreach ($models as $item => $model) {
            $check[$item] = '<i class="fa fa-square-o" aria-hidden="true" style="color: grey"></i>';
            if (!empty($modelsUsers)) {
                if (isset($modelsUsers[$model['id']]) && $modelsUsers[$model['id']]['count'] === $this->modelsCriteriaCount) {
                    $check[$item] = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
                }
            }
            $check[$item] = Html::a($check[$item],
                ['/concourse/default/concourse-answers', 'id' => $this->id, 'objectId' => $model['id'], 'userId' => $userId, 'mode' => 'update'], [
                    'title' => $model['name'] != NULL ? $model['name'] : 'Конкурсная работа ' . $model['id'],
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]
            );
        }
        return implode('', $check);

    }

    public static function getEditableForm($mark, $users_id, $concourse_item_id, $concourse_criteria_id)
    {
        $mark_list = self::mark_list;

        return Editable::widget([
            'buttonsTemplate' => "{reset}{submit}",
            'name' => 'concourse_mark',
            'asPopover' => true,
            'disabled' => false,
            'value' => $mark,
            'header' => 'Выберите оценку',
            'displayValueConfig' => $mark_list,
            'format' => Editable::FORMAT_LINK,
            'inputType' => Editable::INPUT_DROPDOWN_LIST,
            'data' => $mark_list,
            'size' => 'md',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
            'formOptions' => [
                'action' => Url::toRoute(['/concourse/default/set-mark', 'users_id' => $users_id, 'concourse_item_id' => $concourse_item_id, 'concourse_criteria_id' => $concourse_criteria_id]),
            ],
        ]);
    }
}
